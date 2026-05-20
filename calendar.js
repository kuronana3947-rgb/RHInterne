/**
 * Minified by jsDelivr using Terser v5.37.0.
 * Original file: /npm/calendar.js@1.0.6/dist/calendar.js
 *
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
!(function (e) {
  if ("object" == typeof exports && "undefined" != typeof module)
    module.exports = e();
  else if ("function" == typeof define && define.amd) define([], e);
  else {
    ("undefined" != typeof window
      ? window
      : "undefined" != typeof global
        ? global
        : "undefined" != typeof self
          ? self
          : this
    ).Calendar = e();
  }
})(function () {
  return (function e(t, n, a) {
    function i(r, o) {
      if (!n[r]) {
        if (!t[r]) {
          var l = "function" == typeof require && require;
          if (!o && l) return l(r, !0);
          if (s) return s(r, !0);
          var c = new Error("Cannot find module '" + r + "'");
          throw ((c.code = "MODULE_NOT_FOUND"), c);
        }
        var h = (n[r] = { exports: {} });
        t[r][0].call(
          h.exports,
          function (e) {
            var n = t[r][1][e];
            return i(n || e);
          },
          h,
          h.exports,
          e,
          t,
          n,
          a,
        );
      }
      return n[r].exports;
    }
    for (
      var s = "function" == typeof require && require, r = 0;
      r < a.length;
      r++
    )
      i(a[r]);
    return i;
  })(
    {
      1: [
        function (e, t, n) {
          /*!
           * Copyright 2015, Tim Branyen (@tbranyen)
           * calendar.js may be freely distributed under the MIT license.
           */
          var a = e("./util/events"),
            i = e("./util/extend"),
            s = function (e) {
              return [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
              ][e || this.getMonth()];
            },
            r = function (e) {
              return [
                "Sunday",
                "Monday",
                "Tuesday",
                "Wednesday",
                "Thursday",
                "Friday",
                "Saturday",
              ][e || this.getDay()];
            };
          function o(e, t) {
            ((this.el = "string" == typeof e ? document.querySelector(e) : e),
              (this.today = new Date()),
              (this.options = {
                tagName: { month: "table", week: "tr", day: "td" },
                className: { month: "month", week: "week", day: "day" },
              }),
              i(this.options, t),
              i(this, a),
              (this._callbacks = {}));
          }
          ((o.version = e("../package.json").version),
            (o.prototype = {
              init: function () {
                return (
                  this.setDate(new Date()),
                  this.emit("initialize", this),
                  this.update(),
                  this.render(),
                  this
                );
              },
              setDate: function (e) {
                ((this.date = e),
                  i(this.date, { getFullMonth: s, getFullWeek: r }));
              },
              update: function () {
                var e,
                  t,
                  n,
                  a,
                  i,
                  s,
                  r,
                  o = [
                    [0, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0],
                  ],
                  l = new Date(this.date),
                  c = 1,
                  h = l.getMonth();
                for (l.setDate(c); l.getMonth() === h; )
                  ((t = l.getDate()),
                    (e = l.getDay()),
                    (n = Math.floor((t + (6 - e)) / 7)),
                    1 === c && (a = [n, e]),
                    (o[n][e] = { value: new Date(l), type: "day" }),
                    l.setDate(++c));
                return (
                  (s = new Date(l)).setDate(1),
                  s.setMonth(s.getMonth() - 1),
                  o.forEach(function (e, t) {
                    t <= a[0] &&
                      e.forEach(function (n, r) {
                        var o = 7 * t + r,
                          l = 7 * a[0] + a[1];
                        r < (t === a[0] ? a[1] : 7) &&
                          ((i = i ? 1 : -(l - o)),
                          s.setDate(s.getDate() + i),
                          (e[r] = { value: new Date(s), type: "prev" }));
                      }, this);
                  }, this),
                  (r = new Date(l)).setDate(l.getDate() - 1),
                  o.forEach(function (e, t) {
                    t >= n &&
                      e.forEach(function (t, n) {
                        t ||
                          (r.setDate(r.getDate() + 1),
                          (e[n] = { value: new Date(r), type: "next" }));
                      }, this);
                  }, this),
                  (this._month = o),
                  this.emit("update", this),
                  this
                );
              },
              renderDay: function (e, t) {
                ("function" == typeof e && (t = e = "all"),
                  (this._callbacks[e] = t),
                  (this._callbacks.all = t));
              },
              render: function () {
                this.update();
                var e = document.createElement(this.options.tagName.month);
                return (
                  (e.className = this.options.className.month),
                  this.emit("beforeRender", this),
                  this._month.forEach(function (t) {
                    var n = document.createElement(this.options.tagName.week);
                    ((n.className = this.options.className.week),
                      t.forEach(function (e) {
                        var t = document.createElement(
                          this.options.tagName.day,
                        );
                        ((t.className = this.options.className.day),
                          e.value.getFullYear() === this.today.getFullYear() &&
                            e.value.getMonth() === this.today.getMonth() &&
                            e.value.getDate() === this.today.getDate() &&
                            (e.type = "today"),
                          -1 === t.className.split(" ").indexOf(e.type) &&
                            (t.className += " " + e.type),
                          e.value.getFullYear() === this.date.getFullYear() &&
                            e.value.getMonth() === this.date.getMonth() &&
                            e.value.getDate() === this.date.getDate() &&
                            (t.className += " active"),
                          this._callbacks[e.type]
                            ? (this._callbacks[e.type](t, e),
                              this._callbacks.all && this._callbacks.all(t, e))
                            : (t.innerHTML = e.value.getDate() || "&nbsp;"),
                          n.appendChild(t));
                      }, this),
                      e.appendChild(n));
                  }, this),
                  (this.el.innerHTML = ""),
                  this.el.appendChild(e),
                  this.emit("afterRender", this),
                  this
                );
              },
            }),
            (o.Events = a),
            (t.exports = o));
        },
        { "../package.json": 4, "./util/events": 2, "./util/extend": 3 },
      ],
      2: [
        function (e, t, n) {
          var a = {
            callbacks: {},
            on: function (e, t, n) {
              var a = (this.callbacks[e] = this.callbacks[e] || []);
              return ((t._context = n), a.push(t), this);
            },
            once: function (e, t, n) {
              var a = this;
              function i() {
                (a.off(e, i), t.apply(this, arguments));
              }
              return ((t._off = i), this.on(e, i, n), this);
            },
            off: function (e, t) {
              var n,
                a = this.callbacks[e];
              return a
                ? 1 === arguments.length
                  ? (delete this.callbacks[e], this)
                  : (~(n = a.indexOf(t._off || t)) && a.splice(n, 1), this)
                : this;
            },
            emit: function (e) {
              var t,
                n,
                a = [].slice.call(arguments, 1),
                i = this.callbacks[e];
              if (i)
                for (t = 0, n = (i = i.slice(0)).length; t < n; ++t)
                  i[t].apply(i[t]._context || this, a);
              return this;
            },
            listeners: function (e) {
              return this.callbacks[e] || [];
            },
            hasListeners: function (e) {
              return Boolean(this.listeners(e).length);
            },
          };
          t.exports = a;
        },
        {},
      ],
      3: [
        function (e, t, n) {
          var a = Array.prototype.slice;
          t.exports = function (e) {
            var t,
              n,
              i,
              s = a.call(arguments, 1);
            for (t = 0; t < s.length; t++)
              if ("object" == typeof (i = s[t])) for (n in i) e[n] = i[n];
            return e;
          };
        },
        {},
      ],
      4: [
        function (e, t, n) {
          t.exports = {
            name: "calendar.js",
            version: "1.0.6",
            description: "A basic Calendar control",
            main: "dist/calendar.js",
            directories: { example: "example" },
            scripts: {
              build: "npm run browserify",
              browserify:
                "browserify -s Calendar lib/index.js | derequire > dist/calendar.js",
              test: 'echo "Error: no test specified" && exit 1',
            },
            repository: "https://github.com/tbranyen/calendar.js",
            author: "Tim Branyen (@tbranyen)",
            license: "MIT",
            bugs: { url: "https://github.com/tbranyen/calendar.js/issues" },
            homepage: "https://github.com/tbranyen/calendar.js#readme",
            devDependencies: { browserify: "^11.1.0", derequire: "^2.0.2" },
          };
        },
        {},
      ],
    },
    {},
    [1],
  )(1);
});
//# sourceMappingURL=/sm/0b62567721243914bc5f2b16542eae20c110ab65904f89dfe629da803b119d11.map
