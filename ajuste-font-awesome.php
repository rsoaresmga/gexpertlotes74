<?php
if (PHP_SAPI !== 'cli')
{
    die ('Access denied');
}

$replaces_from  = [];
$replaces_to    = [];

$fa = fopen(__DIR__ . '/font-awesome.csv', 'r');
$header = fgetcsv($fa);
while ($row = fgetcsv($fa))
{
	$oldname = $row[0];
	$newname = $row[1];
	$newpref = $row[2];
	
	$replaces_from[] = "fa:{$oldname}";
	$replaces_to[]   = "{$newpref}:{$newname}";
	
	$replaces_from[] = "fa fa-{$oldname}";
	$replaces_to[]   = "{$newpref} fa-{$newname}";
}
fclose($fa);

$replace_paths = ['app/control', 'app/resources', 'menu.xml'];

foreach ($replace_paths as $replace_path)
{
	if (is_file($replace_path))
	{
		replace($replace_path, $replaces_from, $replaces_to);
	}
	else
	{
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($replace_path),
														 RecursiveIteratorIterator::CHILD_FIRST) as $arquivo)
		{
			if ( (substr($arquivo, -4) == '.php') || (substr($arquivo, -5) == '.html') || (substr($arquivo, -3) == '.js'))
			{
				replace($arquivo, $replaces_from, $replaces_to);
			}
		}
	}
}

function replace($arquivo, $replaces_from, $replaces_to)
{
	echo "Processando...: {$arquivo} \n";
	if (is_writable($arquivo))
	{
		$content = file_get_contents($arquivo);
		file_put_contents( $arquivo, str_replace( $replaces_from, $replaces_to, $content) );
	}
	else
	{
		echo "Erro de permissão em...: {$arquivo} \n";
	}
}