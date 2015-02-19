<!doctype html>
<html>
    <head>
    <title>File manager</title>
    <style type="text/css">
		tr:nth-child(2n+3) {background: #f0fff0}
	</style>
    </head>
    <body>
		<?php error_reporting(0);
		require 'class.filemanager.php';
		FileManager::getInstance();
		$list = FileManager::getFileList(); ?>
		<table>
			<tr>
				<th><a href="index.php?sortby=name">name</a></th>
				<th><a href="index.php?sortby=ext">type</a></th>
				<th><a href="index.php?sortby=size">size</a></th>
			</tr>
			<tr>
				<td colspan="3"><a href="index.php?back">..</a></td>
			</tr>
		<?php if(isset($list['directories'])): ?>
			<?php foreach($list['directories'] as $item): ?>
				<tr>
					<td><a href="index.php?gotodir=<?php echo $item['name']; ?>"> <?php echo $item['name']; ?> </a></td>
					<td>folder</td>
					<td><?php echo $item['size'] . ' kB'; ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if(isset($list['files'])): ?>
			<?php foreach($list['files'] as $key => $item): ?>
				<tr>
					<td><?php echo $item['name']; ?></td>
					<td><?php echo $item['ext']; ?></td>
					<td><?php echo $item['size'] . ' kB'; ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</table>
    </body>
</html>

