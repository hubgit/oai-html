<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>OAI</title>
	<style>body { font-family: sans-serif;} a { text-decoration: none; }</style>
</head>

<body>
	<h1><a href="./">OAI</a></h1>

<? if ($info): ?>
	<div><a rel="base-url" href="<? h($info['url']); ?>"><? h($info['name']); ?></a></div>
<? if ($info['sample']): ?>
	<div><a rel="sample-identifier" href="<? h($info['url']); ?>?verb=GetRecord&amp;metadataPrefix=oai_dc&amp;identifier=<? h(urlencode($info['sample'])); ?>">Sample Record</a></div>
<? endif; ?>
<? endif; ?>

<? if ($sets): ?>
	<h2>Sets</h2>

	<ul>
	<? foreach ($sets as $item): ?>
	<li><a rel="set" href="./?server=<? h($baseURL); ?>&amp;set=<? h(urlencode($item['id'])); ?>"><? h($item['name'] ? $item['name'] : $item['id']); ?></a></li>

	<? endforeach; ?>
	</ul>
<? endif; ?>

<? if ($fields): ?>
	<h2>Item</h2>

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
		<input type="hidden" name="server" value="<? h($_GET['server']); ?>">
		<input type="submit" value="GET">
	</form>

	<h2>Items</h2>

	<ul>
	<?foreach ($items as $item): ?>
		<li>
			<div><a rel="item" href="./?server=<? h($baseURL); ?>&amp;id=<? h(urlencode($item['id'])); ?>"><? h($item['title']); ?></a></div>
			<div><? h($item['date']); ?></div>
			<p><? h($item['description']); ?></p>
		</li>
	<? endforeach; ?>
	</ul>
<? endif; ?>

<? if ($links): ?>
	<h2>Links</h2>

	<div class="links">
	<? foreach ($links as $relation => $url): ?>
		<a rel="<? h($relation); ?>" href="<? h($url); ?>"><? h(ucfirst($relation)); ?></a>
	<? endforeach; ?>
	</div>
<? endif; ?>

</body>
</html>