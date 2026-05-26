<?php

/**
 * Wrap Schema::table column additions with Schema::hasColumn guards.
 * Run: php database/migrations/fix_column_migrations.php
 */

$migrationsPath = __DIR__;

$columnTypes = 'unsignedTinyInteger|unsignedInteger|unsignedBigInteger|unsignedSmallInteger|unsignedMediumInteger|tinyInteger|smallInteger|mediumInteger|integer|bigInteger|string|text|longText|mediumText|double|float|decimal|boolean|json|jsonb|date|dateTime|dateTimeTz|time|timeTz|timestamp|year|binary|char|uuid|ipAddress|macAddress';
$addPattern = '/\$table->(' . $columnTypes . ')\s*\(\s*[\'"]([^\'"]+)[\'"]/';

$updated = 0;
$skipped = 0;

foreach (glob($migrationsPath . '/*.php') as $path) {
    if (basename($path) === 'fix_column_migrations.php') {
        continue;
    }

    $content = file_get_contents($path);
    if (!str_contains($content, 'Schema::table(')) {
        continue;
    }
    if (str_contains($content, 'foreach ($columns as $column => $callback)')) {
        $skipped++;
        continue;
    }
    if (!preg_match($addPattern, $content)) {
        continue;
    }

    if (str_contains($content, '->change()')) {
        continue;
    }

    if (preg_match('/add_foreign/i', basename($path))) {
        continue;
    }

    $newContent = processFile($content, $addPattern);
    if ($newContent !== null && $newContent !== $content) {
        file_put_contents($path, $newContent);
        $updated++;
        echo 'Updated: ' . basename($path) . PHP_EOL;
    }
}

echo PHP_EOL . "Done. Updated: {$updated}, Skipped (already guarded): {$skipped}" . PHP_EOL;

function processFile(string $content, string $addPattern): ?string
{
    $modified = false;
    $offset = 0;
    $output = '';

    while (preg_match(
        '/Schema::table\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*function\s*\([^)]*\)\s*\{/s',
        $content,
        $match,
        PREG_OFFSET_CAPTURE,
        $offset
    )) {
        $tableName = $match[1][0];
        $blockStart = $match[0][1];
        $openBrace = $blockStart + strlen($match[0][0]) - 1;
        $bodyEnd = findMatchingBraceEnd($content, $openBrace);
        if ($bodyEnd === null) {
            break;
        }

        $bodyStart = $openBrace + 1;
        $body = substr($content, $bodyStart, $bodyEnd - $bodyStart);

        $output .= substr($content, $offset, $blockStart - $offset);
        $output .= $match[0][0];

        $newBody = transformBody($tableName, $body, $addPattern, $content, $blockStart);
        if ($newBody !== null) {
            $output .= $newBody;
            $modified = true;
        } else {
            $output .= $body;
        }

        $output .= '}';
        $offset = $bodyEnd + 1;
    }

    $output .= substr($content, $offset);

    return $modified ? $output : null;
}

function transformBody(string $tableName, string $body, string $addPattern, string $fullContent, int $blockStart): ?string
{
    $isDown = isDownMethod($fullContent, $blockStart);

    if ($isDown) {
        return transformDownBody($tableName, $body);
    }

    return transformUpBody($tableName, $body, $addPattern);
}

function isDownMethod(string $content, int $pos): bool
{
    $before = substr($content, 0, $pos);
    $upPos = strrpos($before, 'function up');
    $downPos = strrpos($before, 'function down');
    return $downPos !== false && ($upPos === false || $downPos > $upPos);
}

function transformUpBody(string $tableName, string $body, string $addPattern): ?string
{
    $statements = extractStatements($body);
    if ($statements === null) {
        return null;
    }

    $adds = [];
    $raw = [];
    foreach ($statements as $stmt) {
        if (preg_match($addPattern, $stmt, $m) && !preg_match('/\$table->(foreign|dropForeign|dropColumn|renameColumn|rename|index|unique|dropIndex|dropUnique)\s*\(/', $stmt)) {
            $adds[$m[2]] = $stmt;
        } else {
            $raw[] = $stmt;
        }
    }

    if (empty($adds)) {
        return null;
    }

    $indent = detectIndent($body);
    $out = "\n";
    if (!empty($raw)) {
        foreach ($raw as $stmt) {
            $out .= $indent . trim($stmt) . "\n";
        }
        $out .= "\n";
    }

    $out .= $indent . '$columns = [' . "\n";
    foreach ($adds as $name => $stmt) {
        $out .= $indent . "    '{$name}' => function (Blueprint \$table) {\n";
        $out .= $indent . '        ' . trim(preg_replace('/\s+/', ' ', $stmt)) . "\n";
        $out .= $indent . "    },\n";
    }
    $out .= $indent . "];\n\n";
    $out .= $indent . "foreach (\$columns as \$column => \$callback) {\n";
    $out .= $indent . "    if (!Schema::hasColumn('{$tableName}', \$column)) {\n";
    $out .= $indent . "        \$callback(\$table);\n";
    $out .= $indent . "    }\n";
    $out .= $indent . "}\n";

    return $out;
}

function transformDownBody(string $tableName, string $body): ?string
{
    if (!preg_match_all('/\$table->dropColumn\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/', $body, $matches)) {
        if (!preg_match_all('/\$table->dropColumn\s*\(\s*\[\s*([^\]]+)\]/s', $body, $arrayMatches)) {
            return null;
        }
        $cols = [];
        foreach ($arrayMatches[1] as $list) {
            preg_match_all('/[\'"]([^\'"]+)[\'"]/', $list, $cm);
            $cols = array_merge($cols, $cm[1]);
        }
    } else {
        $cols = $matches[1];
    }

    if (empty($cols)) {
        return null;
    }

    $indent = detectIndent($body);
    $out = "\n";
    $out .= $indent . '$columns = [' . "\n";
    foreach ($cols as $col) {
        $out .= $indent . "    '{$col}',\n";
    }
    $out .= $indent . "];\n\n";
    $out .= $indent . "foreach (\$columns as \$column) {\n";
    $out .= $indent . "    if (Schema::hasColumn('{$tableName}', \$column)) {\n";
    $out .= $indent . "        \$table->dropColumn(\$column);\n";
    $out .= $indent . "    }\n";
    $out .= $indent . "}\n";

    return $out;
}

function extractStatements(string $body): ?array
{
    $statements = [];
    $current = '';
    $lines = preg_split('/\r\n|\r|\n/', $body);

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '') {
            continue;
        }
        $current .= ($current === '' ? '' : "\n") . $line;
        if (str_ends_with($trimmed, ';')) {
            $statements[] = trim($current);
            $current = '';
        }
    }

    if ($current !== '') {
        $statements[] = trim($current);
    }

    return $statements ?: null;
}

function findMatchingBraceEnd(string $content, int $openPos): ?int
{
    $depth = 0;
    $len = strlen($content);
    for ($i = $openPos; $i < $len; $i++) {
        $c = $content[$i];
        if ($c === '{') {
            $depth++;
        } elseif ($c === '}') {
            $depth--;
            if ($depth === 0) {
                return $i;
            }
        }
    }
    return null;
}

function detectIndent(string $body): string
{
    if (preg_match('/\n(\s+)\$/', $body, $m)) {
        return $m[1];
    }
    return '            ';
}
