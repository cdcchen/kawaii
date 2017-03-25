<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/9/7
 * Time: 10:37
 */

?>

<script>
  window.addEventListener("load", function (evt) {
    var output = document.getElementById("output");
    var input = document.getElementById("input");
    var ws;
    var print = function (message) {
      var d = document.createElement("div");
      d.innerHTML = message;
      if (output.hasChildNodes())
        output.insertBefore(d, output.firstChild)
      else
        output.appendChild(d);
    };
    document.getElementById("open").onclick = function (evt) {
      if (ws) {
        return false;
      }
      ws = new WebSocket("ws:\/\/127.0.0.1:9513");
      ws.onopen = function (evt) {
        print("OPEN");
        ws.send('xx')
      }
      ws.onclose = function (evt) {
        print("CLOSE");
        ws = null;
      }
      ws.onmessage = function (evt) {
        print(evt.data + "<hr />");
      }
      ws.onerror = function (evt) {
        print("ERROR: " + evt.data + "<br />");
      }
      return false;
    };
    document.getElementById("close").onclick = function (evt) {
      if (!ws) {
        return false;
      }
      ws.close();
      return false;
    };
    document.getElementById("clear").onclick = function (evt) {
      output.innerHTML = '';
      return false;
    };
  });
</script>
<h1><?= $hello ?></h1>
<form>
  <button id="open">Open</button>
  <button id="close">Close</button>
  <button id="clear">Clear</button>
</form>
<hr/>
<div id="output"></div>
