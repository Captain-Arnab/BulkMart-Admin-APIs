/**
 * Shared ApexCharts helpers — VeggiiCart brand palette.
 */
(function (window) {
  'use strict';

  var COLORS = {
    primary: '#12833B',
    forest: '#0B5C27',
    amber: '#F5A623',
    soft: '#3D9B5F',
    muted: '#8AA896',
    error: '#D64545',
  };

  function hideSkeleton(el) {
    if (!el) return;
    var frame = el.closest('.vc-chart-frame, .vc-spark-wrap');
    if (!frame) return;
    var sk = frame.querySelector('[data-skeleton]');
    if (sk) sk.classList.add('is-hidden');
  }

  function sparkline(selector, series, tone, delay) {
    var el = document.querySelector(selector);
    if (!el || typeof ApexCharts === 'undefined') return null;
    var color = tone === 'amber' ? COLORS.amber : COLORS.primary;
    var options = {
      chart: {
        type: 'area',
        height: 46,
        width: 96,
        sparkline: { enabled: true },
        animations: {
          enabled: true,
          easing: 'easeinout',
          speed: 700,
          animateGradually: { enabled: true, delay: delay || 80 },
        },
      },
      series: [{ name: 'trend', data: series || [] }],
      stroke: { curve: 'smooth', width: 2 },
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.45,
          opacityTo: 0.05,
          stops: [0, 90, 100],
        },
      },
      colors: [color],
      tooltip: { enabled: false },
    };
    var chart = new ApexCharts(el, options);
    setTimeout(function () {
      chart.render().then(function () { hideSkeleton(el); });
    }, delay || 0);
    return chart;
  }

  function dualTrend(selector, labels, revenue, orders, height, delay) {
    var el = document.querySelector(selector);
    if (!el || typeof ApexCharts === 'undefined') return null;
    var options = {
      chart: {
        type: 'line',
        height: height || 320,
        toolbar: { show: false },
        animations: {
          enabled: true,
          easing: 'easeinout',
          speed: 900,
          animateGradually: { enabled: true, delay: 120 },
        },
        fontFamily: 'Nunito, Open Sans, sans-serif',
      },
      series: [
        { name: 'Revenue', type: 'area', data: revenue || [] },
        { name: 'Orders', type: 'line', data: orders || [] },
      ],
      stroke: { curve: 'smooth', width: [2, 3] },
      fill: {
        type: ['gradient', 'solid'],
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.55,
          opacityTo: 0.05,
          stops: [0, 90, 100],
        },
      },
      colors: [COLORS.primary, COLORS.forest],
      xaxis: {
        categories: labels || [],
        labels: { rotate: -45, hideOverlappingLabels: true, style: { colors: '#5f6b66', fontSize: '11px' } },
        axisBorder: { show: false },
        axisTicks: { show: false },
      },
      yaxis: [
        {
          title: { text: 'Revenue (₹)', style: { color: COLORS.primary, fontSize: '12px' } },
          labels: {
            formatter: function (v) { return '₹' + Math.round(v).toLocaleString('en-IN'); },
            style: { colors: '#5f6b66' },
          },
        },
        {
          opposite: true,
          title: { text: 'Orders', style: { color: COLORS.forest, fontSize: '12px' } },
          labels: {
            formatter: function (v) { return Math.round(v); },
            style: { colors: '#5f6b66' },
          },
        },
      ],
      dataLabels: { enabled: false },
      grid: { borderColor: 'rgba(11,92,39,0.08)', strokeDashArray: 4 },
      legend: { position: 'top', horizontalAlign: 'right' },
      tooltip: {
        shared: true,
        y: {
          formatter: function (val, opts) {
            if (opts.seriesIndex === 0) return '₹' + Number(val).toLocaleString('en-IN');
            return Math.round(val) + ' orders';
          },
        },
      },
      responsive: [{
        breakpoint: 768,
        options: { chart: { height: 260 }, legend: { position: 'bottom' } },
      }],
    };
    var chart = new ApexCharts(el, options);
    setTimeout(function () {
      chart.render().then(function () { hideSkeleton(el); });
    }, delay || 0);
    return chart;
  }

  function statusDonut(selector, payload, delay, onClick) {
    var el = document.querySelector(selector);
    if (!el || typeof ApexCharts === 'undefined') return null;
    var active = (payload && payload.total_active) || 0;
    var options = {
      chart: {
        type: 'donut',
        height: 300,
        animations: { enabled: true, speed: 800, animateGradually: { enabled: true, delay: 100 } },
        events: {
          dataPointSelection: function (_e, _ctx, cfg) {
            if (typeof onClick === 'function' && payload && payload.keys) {
              onClick(payload.keys[cfg.dataPointIndex]);
            }
          },
        },
      },
      series: (payload && payload.series) || [],
      labels: (payload && payload.labels) || [],
      colors: (payload && payload.colors) || Object.values(COLORS),
      legend: { position: 'bottom', fontSize: '12px' },
      dataLabels: { enabled: false },
      stroke: { width: 2, colors: ['#fff'] },
      plotOptions: {
        pie: {
          donut: {
            size: '68%',
            labels: {
              show: true,
              name: { show: true, fontSize: '12px', color: '#5f6b66', offsetY: -6 },
              value: {
                show: true,
                fontSize: '22px',
                fontWeight: 700,
                color: COLORS.forest,
                formatter: function () { return String(active); },
              },
              total: {
                show: true,
                label: 'Active',
                color: '#5f6b66',
                formatter: function () { return String(active); },
              },
            },
          },
        },
      },
      tooltip: {
        y: { formatter: function (v) { return v + ' orders'; } },
      },
    };
    var chart = new ApexCharts(el, options);
    setTimeout(function () {
      chart.render().then(function () { hideSkeleton(el); });
    }, delay || 0);
    return chart;
  }

  function categoryBars(selector, payload, delay) {
    var el = document.querySelector(selector);
    if (!el || typeof ApexCharts === 'undefined') return null;
    var options = {
      chart: {
        type: 'bar',
        height: 280,
        toolbar: { show: false },
        animations: { enabled: true, speed: 850, animateGradually: { enabled: true, delay: 90 } },
      },
      plotOptions: {
        bar: {
          horizontal: true,
          borderRadius: 6,
          barHeight: '58%',
        },
      },
      series: [{ name: 'Revenue', data: (payload && payload.revenue) || [] }],
      xaxis: {
        categories: (payload && payload.labels) || [],
        labels: {
          formatter: function (v) { return '₹' + Math.round(Number(v)).toLocaleString('en-IN'); },
          style: { colors: '#5f6b66', fontSize: '11px' },
        },
      },
      yaxis: { labels: { style: { colors: '#1E1F22', fontSize: '12px' } } },
      colors: [COLORS.primary],
      fill: {
        type: 'gradient',
        gradient: {
          type: 'horizontal',
          shadeIntensity: 0.35,
          gradientToColors: [COLORS.soft],
          opacityFrom: 1,
          opacityTo: 0.85,
          stops: [0, 100],
        },
      },
      dataLabels: { enabled: false },
      grid: { borderColor: 'rgba(11,92,39,0.08)', xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
      tooltip: {
        y: { formatter: function (v) { return '₹' + Number(v).toLocaleString('en-IN'); } },
      },
    };
    var chart = new ApexCharts(el, options);
    setTimeout(function () {
      chart.render().then(function () { hideSkeleton(el); });
    }, delay || 0);
    return chart;
  }

  function categoryShare(selector, payload, delay) {
    var el = document.querySelector(selector);
    if (!el || typeof ApexCharts === 'undefined') return null;
    var options = {
      chart: {
        type: 'donut',
        height: 260,
        animations: { enabled: true, speed: 800 },
      },
      series: (payload && payload.qty) || [],
      labels: (payload && payload.labels) || [],
      colors: (payload && payload.colors) || [COLORS.primary, COLORS.forest, COLORS.soft, COLORS.amber],
      legend: { position: 'bottom', fontSize: '11px' },
      dataLabels: { enabled: false },
      stroke: { width: 2, colors: ['#fff'] },
      plotOptions: {
        pie: {
          donut: {
            size: '62%',
            labels: {
              show: true,
              total: { show: true, label: 'Volume', fontSize: '12px', color: '#5f6b66' },
            },
          },
        },
      },
      tooltip: {
        y: { formatter: function (v) { return Number(v).toLocaleString('en-IN') + ' units'; } },
      },
    };
    var chart = new ApexCharts(el, options);
    setTimeout(function () {
      chart.render().then(function () { hideSkeleton(el); });
    }, delay || 0);
    return chart;
  }

  window.VCCharts = {
    COLORS: COLORS,
    sparkline: sparkline,
    dualTrend: dualTrend,
    statusDonut: statusDonut,
    categoryBars: categoryBars,
    categoryShare: categoryShare,
    hideSkeleton: hideSkeleton,
  };
})(window);
