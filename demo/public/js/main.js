/**
 * Created by chendong on 2017/4/15.
 */

var app = new Vue({
  el: '#app',
  data: {
    opened: false,
    messages: [],
    ws: null,
    project: ''
  },
  computed: {
    btnLabel: function () {
      return this.opened ? '暂停监控' : '开始监控'
    }
  },
  mounted: function () {
    this.open();
  },
  methods: {
    changeState: function (event) {
      if (this.opened) {
        this.close(event);
      }
      else {
        this.open(event);
      }
    },
    open: function (event) {
      this.ws = new WebSocket(wsHost);
      this.ws.onopen = function (event) {
        this.messages.unshift('开始监控...');
      }.bind(this);
      this.ws.onclose = function (event) {
        this.messages.unshift('暂停监控...');
        this.ws = null;
      }.bind(this);
      this.ws.onmessage = function (event) {
        var log = JSON.parse(event.data);
        if (this.project != '' && log.project != this.project) {
          return;
        }
        this.messages.unshift(log.message + '<hr />');
        if (this.messages.length > 20) {
          this.messages.pop();
        }
      }.bind(this);
      this.ws.onerror = function (event) {
        this.messages.unshift(event.data + '<hr />');
      }.bind(this);

      this.opened = true;
    },
    close: function (event) {
      this.ws.close();
      this.ws = null;
      this.opened = false;
    },
    clear: function (event) {
      this.messages = [];
    },
    send: function (event) {
      var message = JSON.stringify({
        route: '/project',
        data: 'hahahahaha'
      });
      this.ws.send(message);
    }
  },
  watch: {}
});
