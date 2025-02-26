var table;
$(document).ready(function () {
  $('.btn-group.noID').click(function () {
    if ($(event.target).is('label')) {
      $(this).find('i').toggleClass('fa-square-o').toggleClass('fa-check-square-o');
    }
  });

  $('.datepicker').datepicker({
    changeMonth: true,
    changeYear: true,
    showOtherMonths: true,
    selectOtherMonths: true,
    yearRange: '1985:2025',
    defaultDate: '01/01/2001',
  });

  $('#btnAddAnotherShow').click(function () {
    var num_panels = $('.panel-show').length;
    var panel = $('.panel-show:last');
    var new_panel = panel.clone();

    new_panel.find('.panel-heading .panel-num').text(parseInt(num_panels) + 1);

    new_panel.find('input, textarea').each(function () {
      $(this).val('');
    });

    /*new_panel.find(".datepicker").datepicker({
         changeMonth: true,
         changeYear: true,
         showOtherMonths: true,
         selectOtherMonths: true,
         yearRange: '1985:2020',
         defaultDate: '01/01/2001'
      });*/

    new_panel
      .find('.close')
      .show()
      .click(function () {
        $(this).closest('.panel-show').remove();
        return false;
      });

    panel.after(new_panel);
    return false;
  });

  /* var shows = new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.obj.whitespace('team'),
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      remote: {
         url: dvr_ajax_object.ajax_url + 'autocomplete.php?col=' + _col
      }
   });*/

  $('.typeahead').each(function () {
    var containers = {},
      col = $(this).data('typeahead-col');

    containers[col] = new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      prefetch: dvr_ajax_object.ajax_url + 'autocomplete.php?col=' + col,
    });

    $(this)
      .typeahead(
        {
          minLength: 1,
          highlight: true,
        },
        {
          name: $(this).attr('name'),
          display: 'value',
          source: containers[col] /*new Bloodhound({
           datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
           queryTokenizer: Bloodhound.tokenizers.whitespace,
           remote: {
             url: dvr_ajax_object.ajax_url + 'autocomplete.php?col=' + col + '&q=%QUERY',
             wildcard: '%QUERY'
           }
         })*/,
        }
      )
      .bind('typeahead:select', function (ev, suggestion) {
        //console.log(ev);
        //console.log(suggestion.id);
      });
  });

  // Display data
  $('.submitSearch').click(function (e) {
    e.preventDefault();
    table = $('#database').DataTable({
      dom: 'lrtip',
      destroy: true,
      serverSide: true,
      paging: true,
      pageLength: 25,
      processing: true,
      deferRender: true,
      scrollX: true,
      order: [3, 'desc'],
      columnDefs: [
        {
          targets: 'no-sort',
          orderable: false,
        },
      ],
      ajax: {
        url: dvr_ajax_object.ajax_url + 'selectDatabase.php',
        type: 'POST',
        columns: [
          { name: 'DVDID', visible: false },
          { name: 'ShowName' },
          { name: 'Network' },
          { name: 'Date' },
          { name: 'Duration' },
          { name: 'ShowTimePosition' },
          { name: 'Location' },
        ],
        data: function (data) {
          data.custom = {};
          data.custom.search = {};

          data.custom.filterID = $('#noID').prop('checked');

          data.custom.search.airDate = $('#filterAirDate').val();
          data.custom.search.showName = $('#filterShowName').val();
          data.custom.search.DVDID = $('#filterDvdid').val();
          data.custom.search.network = $('#filterNetwork').val();
        },
      },
      drawCallback: function () {
        /*
          * Row grouping
         var api = this.api();
         var rows = api.rows( {page:'current'} ).nodes();
         var last = null;

         api.column(0, {page:'current'} ).data().each( function ( group, i ) {
             if ( last !== group ) {
                 $(rows).eq( i ).before(
                     '<tr class="group"><td colspan="7">'+group+'</td></tr>'
                 );

                 last = group;
             }
         });*/

        $('[data-toggle="popover"]').popover({
          template:
            '<div class="popover" placement="left" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>',
        });

        //Set up edit modal data
        $('.editButton').click(function () {
          var id = $(this).attr('data-id');
          $('#edit-record').find('.modalRecordID').val(id);
          $.ajax(dvr_ajax_object.ajax_url + 'getDatabase.php', {
            type: 'POST',
            data: { recordID: id },
            complete: function (data) {
              data = $.parseJSON(data.responseText);
              var editModalRef = $('#edit-record');
              editModalRef.find('#editAirDate').val(data[0][5]);
              editModalRef.find('#editProgramTime').val(data[0][3]);
              editModalRef.find('#editDVDID').val(data[0][0]);
              editModalRef.find('#editShowName').val(data[0][1]);
              editModalRef.find('#editNetwork').val(data[0][2]);
              editModalRef.find('#editDuration').val(data[0][4]);
              editModalRef.find('#editTimePosition').val(data[0][6]);
              editModalRef.find('#editLocation').val(data[0][7]);
              editModalRef.find('#editComments').val(data[0][8]);
            },
          });
        });

        //Set up delete modal data
        $('.deleteButton').click(function () {
          var id = $(this).attr('data-id');
          $('#delete-record').find('.modalRecordID').val(id);
        });
      },
    });
  });

  // Add Records
  $('#form-add-shows').submit(function (e) {
    e.preventDefault();
    var addModalRef = $('#add-record');
    addModalRef.find('.initial').hide();
    addModalRef.find('.modal-footer').hide();
    $('#addLoading').show();

    var form_data = $(this).serialize();

    var addRecord = $.ajax(dvr_ajax_object.ajax_url + 'addDatabase.php', {
      type: 'POST',
      data: form_data,
      complete: function (data) {
        if (data.responseText == 'true') {
          $('#addLoading').hide();
          addModalRef.find('.processSuccess').show();
          if (table !== undefined) {
            table.draw();
          }
          setTimeout(function () {
            addModalRef.modal('hide');
          }, 2000);
          setTimeout(function () {
            addModalRef.find('form')[0].reset();
            addModalRef.find('.processSuccess').hide();
            addModalRef.find('.initial').show();
            addModalRef.find('.modal-footer').show();
          }, 4000);
        } else {
          //Error Handling
          $('#addLoading').hide();
          addModalRef.find('.processError').html(data.responseText);
          addModalRef.find('.processError').show();
          addModalRef.find('.processFailure').show();
          addModalRef.find('.processClose').show();
          addModalRef.find('.processClose').click(function () {
            addModalRef.modal('hide');
            setTimeout(function () {
              addModalRef.find('.processError').hide();
              addModalRef.find('.processFailure').hide();
              addModalRef.find('.processClose').hide();
              addModalRef.find('.initial').show();
              addModalRef.find('.modal-footer').show();
            }, 1000);
          });
        }
      },
    });

    return false;
  });

  /*
  $("#add-record").find(".add-record-submit").click(function(e) {
    e.preventDefault();
    var addModalRef = $("#add-record");
    addModalRef.find(".initial").hide();
    addModalRef.find(".modal-footer").hide();
    $('#addLoading').show();
    var json = {};



    if(addModalRef.find("#DVDID").val().length > 0) {
        for (count = 0; count < 10; count++) {
          /*var json_iter = {
           dvdID: addModalRef.find("#DVDID").val(),
           showName: addModalRef.find("#ShowName").val(),
           network: addModalRef.find("#Network").val(),
           airDate: addModalRef.find("#AirDate").val(),
           programTime: addModalRef.find("#ProgramTime").val(),
           location: addModalRef.find("#Location").val(),
           duration: addModalRef.find("#Duration").val(),
           comments: addModalRef.find("#Comments").val()
           };*/
  /*if (addModalRef.find("#ShowName" + count).val().length > 0 && addModalRef.find("#AirDate" + count).val().length > 0 && addModalRef.find("#Network" + count).val().length > 0) { */
  /* json["dvdID" + count] = addModalRef.find("#DVDID").val();
                json["showName" + count] = addModalRef.find("#ShowName" + count).val();
                json["network" + count] = addModalRef.find("#Network" + count).val();
                json["airDate" + count] = addModalRef.find("#AirDate" + count).val();
                json["programTime" + count] = addModalRef.find("#ProgramTime" + count).val();
                json["location" + count] = addModalRef.find("#Location" + count).val();
                json["duration" + count] = addModalRef.find("#Duration" + count).val();
                json["comments" + count] = addModalRef.find("#Comments" + count).val();
           /* }  */
  /* }
    }


    var addRecord = $.ajax(dvr_ajax_object.ajax_url + "addDatabase.php", {
      type: "POST",
      data: json,
      complete: function (data) {
        if (data.responseText == "true") {
          $('#addLoading').hide();
          addModalRef.find(".processSuccess").show();
          if (table !== undefined) {
            table.draw();
          }
          setTimeout(function() {
            addModalRef.modal("hide");
          }, 2000);
          setTimeout(function() {
            addModalRef.find("form")[0].reset();
            addModalRef.find(".processSuccess").hide();
            addModalRef.find(".initial").show();
            addModalRef.find(".modal-footer").show();
          }, 4000);
        } else {
          //Error Handling
          $('#addLoading').hide();
          addModalRef.find(".processError").html(data.responseText);
          addModalRef.find(".processError").show();
          addModalRef.find(".processFailure").show();
          addModalRef.find(".processClose").show();
          addModalRef.find(".processClose").click(function () {
            addModalRef.modal("hide");
            setTimeout(function() {
              addModalRef.find(".processError").hide();
              addModalRef.find(".processFailure").hide();
              addModalRef.find(".processClose").hide();
              addModalRef.find(".initial").show();
              addModalRef.find(".modal-footer").show();
            }, 1000);
          });
        }
      }
    });

    return false;
  });

  */

  //Edit Record
  $('#edit-record')
    .find('.edit-record-submit')
    .click(function (e) {
      e.preventDefault();
      var editModalRef = $('#edit-record');
      editModalRef.find('.initial').hide();
      editModalRef.find('.modal-footer').hide();
      $('#editLoading').show();
      var json = {
        recordID: editModalRef.find('.modalRecordID').val(),
        dvdID: editModalRef.find('#editDVDID').val(),
        showName: editModalRef.find('#editShowName').val(),
        network: editModalRef.find('#editNetwork').val(),
        airDate: editModalRef.find('#editAirDate').val(),
        programTime: editModalRef.find('#editProgramTime').val(),
        timePosition: editModalRef.find('#editTimePosition').val(),
        location: editModalRef.find('#editLocation').val(),
        duration: editModalRef.find('#editDuration').val(),
        comments: editModalRef.find('#editComments').val(),
      };
      var editRecord = $.ajax(dvr_ajax_object.ajax_url + 'updateDatabase.php', {
        type: 'POST',
        data: json,
        complete: function (data) {
          if (data.responseText == 'true') {
            $('#editLoading').hide();
            editModalRef.find('.processSuccess').show();
            if (table !== undefined) {
              table.draw();
            }
            setTimeout(function () {
              editModalRef.modal('hide');
            }, 2000);
            setTimeout(function () {
              editModalRef.find('.processSuccess').hide();
              editModalRef.find('.initial').show();
              editModalRef.find('.modal-footer').show();
            }, 4000);
          } else {
            //Error Handling
            $('#editLoading').hide();
            editModalRef.find('.processError').html(data.responseText);
            editModalRef.find('.processError').show();
            editModalRef.find('.processFailure').show();
            editModalRef.find('.processClose').show();
            editModalRef.find('.processClose').click(function () {
              editModalRef.modal('hide');
              setTimeout(function () {
                editModalRef.find('.processError').hide();
                editModalRef.find('.processFailure').hide();
                editModalRef.find('.processClose').hide();
                editModalRef.find('.initial').show();
                editModalRef.find('.modal-footer').show();
              }, 1000);
            });
          }
        },
      });

      return false;
    });

  // Delete Record
  $('#delete-record')
    .find('.delete-record-submit')
    .click(function (e) {
      e.preventDefault();
      var deleteModalRef = $('#delete-record');
      deleteModalRef.find('.initial').hide();
      deleteModalRef.find('.modal-footer').hide();
      $('#deleteLoading').show();
      var recordID = { recordID: deleteModalRef.find('.modalRecordID').val() };
      var deleteRecord = $.ajax(dvr_ajax_object.ajax_url + 'deleteDatabase.php', {
        type: 'POST',
        data: recordID,
        complete: function (data) {
          if (data.responseText == 'true') {
            $('#deleteLoading').hide();
            deleteModalRef.find('.processSuccess').show();
            if (table !== undefined) {
              table.draw();
            }
            setTimeout(function () {
              deleteModalRef.modal('hide');
            }, 2000);
            setTimeout(function () {
              deleteModalRef.find('.processSuccess').hide();
              deleteModalRef.find('.initial').show();
              deleteModalRef.find('.modal-footer').show();
            }, 2500);
          } else {
            //Error Handling
            $('#deleteLoading').hide();
            deleteModalRef.find('.processError').html(data.responseText);
            deleteModalRef.find('.processError').show();
            deleteModalRef.find('.processFailure').show();
            deleteModalRef.find('.processClose').show();
            deleteModalRef.find('.processClose').click(function () {
              deleteModalRef.modal('hide');
              setTimeout(function () {
                deleteModalRef.find('.processError').hide();
                deleteModalRef.find('.processFailure').hide();
                deleteModalRef.find('.processClose').hide();
                deleteModalRef.find('.initial').show();
                deleteModalRef.find('.modal-footer').show();
              }, 500);
            });
          }
        },
      });
    });

  return false;
});
