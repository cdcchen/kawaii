/**
 * Created by chendong on 2017/4/15.
 */

var app = new Vue({
  el: '#app',
  data: {
    opened: false,
    messages: [],
    ws: null
  },
  computed: {
    btnLabel: function () {
      return this.opened ? '暂停监控' : '开始监控'
    }
  },
  methods: {
    changeState: function (event) {
      if (this.opened) {
        this.close(event);
      }
      else {
        this.open(event);
      }
      this.opened = !this.opened;
    },
    open: function (event) {
      this.ws = new WebSocket('ws://127.0.0.1:9527/log');
      this.ws.onopen = function (event) {
        this.messages.unshift('开始监控...');
      }.bind(this);
      this.ws.onclose = function (event) {
        this.messages.unshift('暂停监控...');
        this.ws = null;
      }.bind(this);
      this.ws.onmessage = function (event) {
        this.messages.unshift(event.data + '<hr />');
      }.bind(this);
      this.ws.onerror = function (event) {
        this.messages.unshift(event.data + '<hr />');
      }.bind(this);
    },
    close: function (event) {
      this.ws.close();
      this.ws = null;
    },
    clear: function (event) {
      this.messages = [];
    }
  },
  watch: {}
});
