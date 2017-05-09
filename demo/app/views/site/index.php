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
    <select v-model="project">
      <option value="">所有项目</option>
      <option value="webappp">webapp</option>
      <option value="exam">exam</option>
      <option value="console">console</option>
      <option value="backend">backend</option>
      <option value="openapi">openapi</option>
      <option value="console">console</option>
      <option value="paper">paper</option>
      <option value="passport">passport</option>
      <option value="school_manage">school-manage</option>
      <option value="wechat">wechat</option>
    </select>
    <button type="button" class="btn btn-primary" @click="changeState">{{btnLabel}}</button>
    <button type="button" class="btn btn-outline-info" @click="clear">清除内容</button>
    <button type="button" class="btn btn-outline-info" @click="send">发送内容</button>
  </form>
  <hr/>

  <template v-for="msg in messages">
    <pre v-html="msg"></pre>
  </template>
</div>