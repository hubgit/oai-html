<form>
  <div><label>Set <input type="text" name="set" value="<? h($_GET['set']); ?>" size="50"></label></div>
  <div><label>From <input type="date" name="from" value="<? h($_GET['from']); ?>"></label></div>
  <div><label>Until <input type="date" name="until" value="<? h($_GET['until']); ?>"></label></div>
  <input type="hidden" name="server" value="<? h($_GET['server']); ?>">
  <input type="submit" value="GET">
</form>

<h2>Items</h2>

<ul class="entries">
<?foreach ($entries as $entry): ?>
  <li>
    <? require __DIR__ . '/entry.html.php'; ?>
  </li>
<? endforeach; ?>
</ul>