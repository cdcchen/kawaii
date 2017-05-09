<?php
/**
 * @var string $wsHost
 */
?>

<script type="text/javascript">
  var wsHost = '<?= $wsHost ?>';
</script>

<div id="app">
  <form class="form-inline text-center my-2">
    <button type="button" class="btn btn-primary" @click="changeState">{{btnLabel}}</button>
    <button type="button" class="btn btn-outline-info" @click="clear">清除内容</button>
    <button type="button" class="btn btn-outline-info" @click="send">发送内容</button>
  </form>
  <hr/>

  <template v-for="msg in messages">
    <pre v-html="msg"></pre>
  </template>
</div>