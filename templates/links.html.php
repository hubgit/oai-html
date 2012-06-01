  <h2>Links</h2>

  <div class="links">
  <? foreach ($links as $relation => $url): ?>
    <a rel="<? h($relation); ?>" href="<? h($url); ?>"><? h(ucfirst($relation)); ?></a>
  <? endforeach; ?>
  </div>