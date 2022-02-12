<?php
if (PHP_SAPI !== 'cli')
{
    die ('Access denied');
}


$replace_paths = ['app/control'];

foreach ($replace_paths as $replace_path)
{
	if (is_file($replace_path))
	{
		replace($replace_path, 'btn-primary', 'btn-success');
	}
	else
	{
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($replace_path),
														 RecursiveIteratorIterator::CHILD_FIRST) as $arquivo)
		{
			if ( (substr($arquivo, -4) == '.php') || (substr($arquivo, -5) == '.html') )
			{
				replace($arquivo, 'btn-primary', 'btn-success');
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