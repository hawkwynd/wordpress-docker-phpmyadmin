jQuery(document).ready(function($) {
  'use strict';

  var post_id = $("#post_ID").val();
  var $container = $("#lptw-table-data");

  // Uploading files

  function open_media_uploader(target_row, target_col) {

    var send_attachment_bkp = wp.media.editor.send.attachment;

    wp.media.editor.send.attachment = function(props, attachment) {

      var img_align = props.align;
      var img_embed = props.canEmbed;
      var img_link = props.link;
      var img_size = props.size;

      var a_start = '', a_end = '', result_data;

      if (img_link == 'file') {
        a_start = '<a href="' + attachment.url + '">';
        a_end = '</a>';
      } else if (img_link == 'post') {
        a_start = '<a href="' + attachment.link + '">';
        a_end = '</a>';
      } else if (img_link == 'custom') {
        a_start = '<a href="' + props.linkUrl + '">';
        a_end = '</a>';
      }

      var result_image = a_start + '<img src="' + attachment.sizes[img_size].url + '" class="align' + img_align + ' size-' + img_size + ' wp-image-' + attachment.id + '" height="' + attachment.sizes[img_size].height + '" width="' + attachment.sizes[img_size].width + '">' + a_end;

      var cell_data = $("#lptw-table-data").data("handsontable").getDataAtCell(target_row, target_col);
      if (cell_data) {
        result_data = cell_data + result_image;
      } else {
        result_data = result_image;
      }
      $("#lptw-table-data").data("handsontable").setDataAtCell(target_row, target_col, result_data);
      $("#lptw-table-data").handsontable('render');

      wp.media.editor.send.attachment = send_attachment_bkp;
    }

    wp.media.editor.open();

  }

  $container.resizable({
    containment: "#lptw_advanced_table",
    stop: function(event, ui) {
      $container.handsontable('render');
    }
  });

  $('#publish').one('click', function(event) {
    event.preventDefault();
    saveTableData(post_id);
    saveTableMeta(post_id);
    saveCellsMeta(post_id);
    // Now trigger click on button again
    $('#footer-row input[type=checkbox]').each(function(index) {
      $(this).val(index);
    })
    $('#publish').trigger('click');
  });

  $('#generate_shortcode').click(function(e) {
    var sb_style = $('#post_shortcode_table_style option:selected').val();
    var shortcode = '[lptw_table id="' + post_id + '" style="' + sb_style + '"]';
    $('#table_shortcode').val(shortcode);
    $('#table_shortcode').addClass('ready');
    setTimeout(function() {
      $('#table_shortcode').removeClass('ready');
    }, 5000);
    e.preventDefault();
  });

  $('#insert-media-button').click(function() {
    var selected_cell = $container.data('handsontable').getSelected();
    if (selected_cell) {
      var target_row = selected_cell[0];
      var target_col = selected_cell[1];
      open_media_uploader(target_row, target_col);
    } else {
      $('#insert-media-button-message').text('You need select a cell first!');
      setTimeout(function() {
        $('#insert-media-button-message').text('');
      }, 5000);
    }
  });

  $container.handsontable({
    data: getTableData(),
    contextMenu: true,
    rowHeaders: false,
    stretchH: 'all',
    colHeaders: true,
    minSpareCols: 0,
    minSpareRows: 0,
    cell: getCellsMeta(),
    mergeCells: getTableMeta(),
    renderer: safeHtmlRenderer,
    outsideClickDeselects: false,
    afterChange: function() {
      saveTableData(post_id);
      saveTableMeta(post_id);
    },
    afterSetCellMeta: function(row, col, key, val) {
      if (typeof val === "string") {
        var json_meta_local = JSON.stringify({
          "row": row,
          "col": col,
          "className": val
        });
        var stored_data = $("#lptw-table-storage").text();
        if (stored_data != '') {
          var result_object = $.parseJSON("[" + stored_data + "]");
          var cleared_stored_data = "";
          $.each(result_object, function(item, value) {
              if (value.row != row || value.col != col) {
                  var cleared_row = JSON.stringify({
                      "row": value.row,
                      "col": value.col,
                      "className": value.className
                  });
                  if (cleared_stored_data != "") {
                      cleared_stored_data = cleared_stored_data + "," + cleared_row;
                  } else {
                      cleared_stored_data = cleared_row;
                  }
              }
          });
          if (cleared_stored_data === undefined) {
            stored_data = json_meta_local;
          } else {
            stored_data = cleared_stored_data + "," + json_meta_local;
          }
          $("#lptw-table-storage").text(stored_data);
        } else {
          $("#lptw-table-storage").text(json_meta_local);
        }
      }
    },
    afterCreateCol: function(index) {
      var row = document.getElementById('footer-row');
      var td = row.insertCell(index);
      td.innerHTML = '<label title="Count totals"><input type="checkbox" name="totals[]">&nbsp;Totals</label>';
    },
    afterRemoveCol: function(index) {
      var row = document.getElementById('footer-row');
      row.deleteCell(index);
    },
    afterRender: function() {
      var count_cols = $container.data("handsontable").countCols();
      var totals = getTableTotals();
      var totals_arr = $.parseJSON(JSON.stringify(totals));

      var table = $container.find('.htCore')[0];
      if (table.tFoot === null) {
        var footer = table.createTFoot();
        var row = footer.insertRow(0);
        row.id = 'footer-row';
        for (var i = 0; i < count_cols; i++) {
          var cell = row.insertCell(i);
          if (jQuery.inArray(i, totals_arr) >= 0) {
            var checked = 'checked="checked"';
          } else {
            var checked = '';
          }
          cell.innerHTML = '<label title="Count totals"><input type="checkbox" name="totals[]" ' + checked + '>&nbsp;Totals</label>';
        }
      }
    }
  });

  function saveTableMeta(post_id) {
    if ($("#lptw-table-data").data("handsontable").mergeCells) {
      var table_meta = $("#lptw-table-data").data("handsontable").mergeCells.mergedCellInfoCollection;

      $.ajax({
        url: myAjax.ajaxurl,
        data: {
          "action": "save_table_meta",
          "data": table_meta,
          "post_id": post_id
        },
        dataType: "json",
        type: "POST",
        async: false,
        success: function(res) {
          if (res.result === "ok") {
            $("#lptw-table-message").html("meta saved").delay(1000).css("display", "none");
          } else {
            $("#lptw-table-message").html("error!").delay(1000).css("display", "none");
          }
        }
      });
    }
  }

  function saveCellsMeta(post_id) {
    var cells_meta = $("#lptw-table-storage").text();
    if (cells_meta != "") {
      cells_meta = $.parseJSON('[' + cells_meta + ']');

      $.ajax({
        url: myAjax.ajaxurl,
        data: {
          "action": "save_cells_meta",
          "data": cells_meta,
          "post_id": post_id
        },
        dataType: "json",
        type: "POST",
        async: false,
        success: function(res) {
          if (res.result === "ok") {
            $("#lptw-table-message").html("cells meta saved").delay(1000).css("display", "none");
          } else {
            $("#lptw-table-message").html("error!").delay(1000).css("display", "none");
          }
        }
      });
    }
  }

  function saveTableData(post_id) {
    var handsontable = $container.data("handsontable");
    $("#lptw-table-message").css("display", "block").html("saving data...");
    var table_data = handsontable.getData();
    var table_data_string = JSON.stringify(table_data);
    var escaped_table_data_string = table_data_string.replace(/\\n/g, "\\n")
      .replace(/\\&/g, "\\&")
      .replace(/'/g, "&#39;")
      .replace(/\\"/g, '&quot;')
      .replace(/\\r/g, "\\r")
      .replace(/\\t/g, "\\t")
      .replace(/\\b/g, "\\b")
      .replace(/\\f/g, "\\f");
    $.ajax({
      url: myAjax.ajaxurl,
      scriptCharset: "utf-8",
      data: {
        "action": "save_table_data",
        "data": escaped_table_data_string,
        "post_id": post_id
      }, //returns all cells data
      dataType: "json",
      type: "POST",
      success: function(res) {
        if (res.result === "ok") {
          $("#lptw-table-message").html("data saved").delay(1000).css("display", "none");
        } else {
          $("#lptw-table-message").html("error!").delay(1000).css("display", "none");
        }
      },
      error: function() {
        $("#lptw-table-message").html("no data").delay(1000).css("display", "none");
      }
    });
  }

  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  function strip_tags(input, allowed) {
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
      commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;

    // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
    allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');

    return input.replace(commentsAndPhpTags, '').replace(tags, function($0, $1) {
      return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
  }

  function safeHtmlRenderer(instance, td, row, col, prop, value, cellProperties) {
    var escaped = Handsontable.helper.stringify(value);
    escaped = strip_tags(escaped, '<em><b><strong><a><big><small><img><h1><h2><h3><h4><span><sub><sup><br>'); //be sure you only allow certain HTML tags to avoid XSS threats (you should also remove unwanted HTML attributes)
    td.innerHTML = escaped;
    td.className = cellProperties.className;

    return td;
  }


});
