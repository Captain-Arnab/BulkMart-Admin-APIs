/**
 * Dashboard chart bootstrap.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    if (!window.VC_DASHBOARD || !window.VCCharts) return;
    var data = window.VC_DASHBOARD;
    var sparks = data.sparklines || {};
    var tones = { orders: 'primary', pending: 'amber', revenue: 'primary', low_stock: 'amber' };
    var sparkMap = {
      orders: sparks.orders || [],
      pending: sparks.pending || [],
      revenue: sparks.revenue || [],
      low_stock: sparks.demand || [],
    };

    var delay = 0;
    Object.keys(sparkMap).forEach(function (key) {
      VCCharts.sparkline('#spark-' + key, sparkMap[key], tones[key], delay);
      delay += 70;
    });

    var trendChart = null;
    function renderTrend(days) {
      var pack = (data.trends && data.trends[String(days)]) || { labels: [], revenue: [], orders: [] };
      var el = document.querySelector('#chart-trend');
      if (!el) return;
      if (trendChart) {
        trendChart.updateOptions({
          xaxis: { categories: pack.labels },
          series: [
            { name: 'Revenue', type: 'area', data: pack.revenue },
            { name: 'Orders', type: 'line', data: pack.orders },
          ],
        });
        return;
      }
      trendChart = VCCharts.dualTrend('#chart-trend', pack.labels, pack.revenue, pack.orders, 320, 120);
    }

    renderTrend(30);

    document.querySelectorAll('[data-trend-range]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.querySelectorAll('[data-trend-range]').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        var days = parseInt(btn.getAttribute('data-trend-range'), 10) || 30;
        renderTrend(days);
        var link = btn.closest('.card-body').querySelector('.vc-view-report');
        if (link) {
          link.setAttribute('href', link.getAttribute('href').replace(/preset=\d+d/, 'preset=' + days + 'd'));
        }
      });
    });

    VCCharts.statusDonut('#chart-status', data.status, 200);
    VCCharts.categoryBars('#chart-categories', data.categories, 280);
  });
})();
