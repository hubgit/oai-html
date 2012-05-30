<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>OAI</title>
	<style>body { font-family: sans-serif;} a { text-decoration: none; }</style>
</head>

<body>
	<h1><a href="./">OAI</a></h1>

<? if ($description): ?>
	<div><a href="<? h($description['url']); ?>"><? h($description['name']); ?></a></div>
<? if ($description['sample']): ?>
	<div><a href="<? h($description['url']); ?>?verb=GetRecord&amp;metadataPrefix=oai_dc&amp;identifier=<? h(urlencode($description['sample'])); ?>">Sample Record</a></div>
<? endif; ?>
<? endif; ?>

<? if ($sets): ?>
	<ul>
	<? foreach ($sets as $item): ?>
	<li><a href="./?set=<? h(urlencode($item['id'])); ?>"><? h($item['name']); ?></a></li>

	<? endforeach; ?>
	</ul>
<? endif; ?>

<? if ($fields): ?>
	<table>
	<?foreach ($fields as $field): ?>
		<tr>
			<th><? h($field['field']); ?></th>
			<td><? h($field['value']); ?></td>
		</tr>
	<? endforeach; ?>
	</table>
<? endif; ?>

<? if ($items): ?>
	<form>
		<div><label>Set <input type="text" name="set" value="<? h($_GET['set']); ?>"></label></div>
		<div><label>From <input type="date" name="from" value="<? h($_GET['from']); ?>"></label></div>
		<div><label>Until <input type="date" name="until" value="<? h($_GET['until']); ?>"></label></div>
		<input type="submit" value="GET">
	</form>

	<ul>
	<?foreach ($items as $item): ?>
		<li>
			<div><a href="./?id=<? h(urlencode($item['id'])); ?>"><? h($item['title']); ?></a></div>
			<div><? h($item['date']); ?></div>
			<p><? h($item['description']); ?></p>
		</li>
	<? endforeach; ?>
	</ul>
<? endif; ?>

<? if ($links): ?>
<div class="links">
	<h2>Links</h2>
<? foreach ($links as $relation => $url): ?>
	<a rel="relation" href="<? h($url); ?>"><? h(ucfirst($relation)); ?></a>
<? endforeach; ?>
</div>
<? endif; ?>

</body>
</html>