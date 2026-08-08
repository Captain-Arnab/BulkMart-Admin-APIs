/**
 * Reports & Analytics chart bootstrap.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    if (!window.VC_REPORTS || !window.VCCharts) return;
    var data = window.VC_REPORTS;
    var filters = window.VC_REPORTS_FILTERS || {};

    VCCharts.dualTrend('#report-trend', data.trend.labels, data.trend.revenue, data.trend.orders, 360, 80);
    VCCharts.statusDonut('#report-status', data.status, 160, function (statusKey) {
      var input = document.getElementById('filter-status');
      var form = document.getElementById('reports-filter-form');
      if (!input || !form) return;
      input.value = statusKey;
      // Keep custom dates when filtering by status
      var preset = form.querySelector('input[name="preset"]');
      if (preset) preset.value = 'custom';
      form.submit();
    });
    VCCharts.categoryBars('#report-category-bars', data.categories, 240);
    VCCharts.categoryShare('#report-category-share', data.categories, 320);

    // Deep-link hash scroll
    if (window.location.hash) {
      var target = document.querySelector(window.location.hash);
      if (target) {
        setTimeout(function () {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 200);
      }
    }

    // Highlight active status filter on table if present
    if (filters.status) {
      document.querySelectorAll('#orders-detail-table tbody tr').forEach(function (row) {
        if (row.getAttribute('data-status') !== filters.status) {
          row.classList.add('opacity-50');
        }
      });
    }
  });
})();
