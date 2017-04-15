<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/9/7
 * Time: 10:37
 */

?>

<div id="app">
  <form class="form-inline text-center my-2">
    <button type="button" class="btn btn-primary" @click="changeState">{{btnLabel}}</button>
    <button type="button" class="btn btn-outline-info" @click="clear">清除内容</button>
  </form>
  <hr/>

  <template v-for="msg in messages">
    <p v-html="msg"></p>
  </template>
</div>

<div id="output"></div>