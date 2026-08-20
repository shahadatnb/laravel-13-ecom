/**
 * VariantExport — client-side CSV export for the variants table.
 * Usage:
 *   VariantExport.init(tableBodyId)
 *   // or pass an existing button:
 *   VariantExport.wire(buttonElement, tableBodyElement)
 */
(function (global) {
  'use strict';

  var VariantExport = {};

  /**
   * Read the table-header row to build the CSV column list.
   * Returns an array of column names (strings).
   */
  function buildHeaders(theadTr) {
    var headers = [];
    if (!theadTr) return headers;

    var ths = theadTr.querySelectorAll('th');
    ths.forEach(function (th) {
      // Get clean text, strip child-element text (Upload… hints)
      var text = '';
      var thClone = th.cloneNode(true);
      // Remove small / sup / sub child elements so we only keep the main name
      var toRemove = thClone.querySelectorAll('small, sup, sub, span.text-muted');
      toRemove.forEach(function (el) { el.remove(); });
      text = thClone.textContent.trim().replace(/\s+/g, ' ');

      if (text && text !== 'Images' && text !== 'Action') {
        headers.push(text);
      }
    });

    return headers;
  }

  /**
   * Try to guess headers from a variant row's [attributes] hidden input.
   */
  function guessHeadersFromRow(row) {
    var headers = [];
    var attrInput = row && row.querySelector('input[name$="[attributes]"]');
    if (attrInput) {
      try {
        var attrs = JSON.parse(attrInput.value);
        Object.keys(attrs).forEach(function (k) { headers.push(k); });
      } catch (e) { /* skip */ }
    }
    headers.push('SKU', 'Price', 'Stock');
    return headers;
  }

  /**
   * Escape a cell value for CSV (wrap in quotes, double internal quotes).
   */
  function csvCell(val) {
    var s = String(val == null ? '' : val);
    return '"' + s.replace(/"/g, '""') + '"';
  }

  /**
   * Generate CSV text from the variants table body element.
   */
  function generateCSV(tableBody) {
    if (!tableBody) return '';

    var rows = tableBody.querySelectorAll('tr');
    if (rows.length === 0) return '';

    // ── Headers ──
    var theadTr = document.querySelector('#variants-table thead tr');
    var headers = buildHeaders(theadTr);
    if (headers.length === 0) {
      headers = guessHeadersFromRow(rows[0]);
    }

    var csvRows = [];
    csvRows.push(headers.map(csvCell).join(','));

    // ── Data rows ──
    rows.forEach(function (row) {
      var cols = [];

      // Gather visible attribute-value text nodes from the row's TDs.
      // The variant table layout is: [attr cols] [Images] [SKU] [Price] [Stock] [Action]
      // We grab text from TDs that don't contain buttons / images / inputs.
      var tds = row.querySelectorAll('td');
      var attrValues = [];
      tds.forEach(function (td) {
        if (td.querySelector('button, img, .variant-image-cell, .variant-preview-img')) return;
        if (td.querySelector('input')) return; // SKU/Price/Stock are in input-inside-TD
        var txt = td.textContent.trim();
        if (txt) attrValues.push(txt);
      });

      // Read SKU / Price / Stock from named inputs
      var inputs = row.querySelectorAll('input');
      var sku = '', price = '', stock = '';
      inputs.forEach(function (inp) {
        var name = inp.getAttribute('name') || '';
        if (/\[sku\]/.test(name))       sku   = inp.value;
        else if (/\[price\]/.test(name)) price = inp.value;
        else if (/\[stock\]/.test(name)) stock = inp.value;
      });

      attrValues.forEach(function (v) { cols.push(csvCell(v)); });
      cols.push(csvCell(sku));
      cols.push(csvCell(price));
      cols.push(csvCell(stock));

      csvRows.push(cols.join(','));
    });

    return csvRows.join('\n');
  }

  /**
   * Trigger a browser download of the CSV.
   */
  function downloadCSV(csvContent, filename) {
    // BOM for Excel UTF-8 support
    var blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename || ('variants-export-' + new Date().toISOString().slice(0, 10) + '.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(link.href);
  }

  /**
   * Main export action — called by the button handler.
   */
  function doExport(tableBody) {
    var rows = tableBody.querySelectorAll('tr');
    if (rows.length === 0) {
      alert('No variants to export. Generate variants first.');
      return;
    }

    var csv = generateCSV(tableBody);
    if (!csv) {
      alert('Could not generate CSV. Make sure variants exist.');
      return;
    }

    downloadCSV(csv);
  }

  // ── Public API ──

  /** Initialize from the variants table body ID. */
  VariantExport.init = function (tableBodyId) {
    var btn = document.getElementById('export-csv-btn');
    var tbody = document.getElementById(tableBodyId || 'variants-table-body');
    if (btn && tbody) {
      btn.addEventListener('click', function () { doExport(tbody); });
    }
  };

  /** Wire a specific button + table-body pair. */
  VariantExport.wire = function (buttonEl, tableBodyEl) {
    if (buttonEl && tableBodyEl) {
      buttonEl.addEventListener('click', function () { doExport(tableBodyEl); });
    }
  };

  /** Expose utility for testing. */
  VariantExport._generateCSV = generateCSV;
  VariantExport._downloadCSV = downloadCSV;

  global.VariantExport = VariantExport;
})(window);
