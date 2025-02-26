<?php
require_once '../vendor/autoload.php';
session_start();

// Check for session variables
$user_logged_in = isset($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MRC DVR</title>
  <!-- Local CSS -->
  <link rel="stylesheet" href="./css/dvr_ajax.css" />

  <!-- External CSS -->
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css" />
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css?ver=5.7.2" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>

<body>
  <?php if (!$user_logged_in): ?>
    <div class="login-button-container">
      <h1>DVR Manager</h1>
      <a href="login.php" class="google-login-button">
        <span class="google-icon">
          <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google Icon">
        </span>
        <span class="button-text">Login with Google</span>
      </a>
    </div>
  <?php else: ?>

    <div class="content container">
      <div class="user-detials">

        <p>Hello, <strong><?php echo htmlspecialchars($_SESSION['user']->name); ?></strong></p>
        <a href="logout.php" class="logout">Logout</a>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <button type="button" class="btn btn-success navbar-btn" data-toggle="modal" data-target="#add-record">
            Add Record
          </button>
          <form class="navbar-form navbar-right search" role="search">
            <div class="form-group">
              <div class="btn-group noID" data-toggle="buttons">
                <label for="noID" class="btn btn-info">
                  <input type="checkbox" id="noID" />
                  <i class="fa fa-square-o"></i> Show no ID
                </label>
              </div>
              <input type="text" id="filterAirDate" class="form-control airDate datepicker" placeholder="Air Date" />
              <input type="text" id="filterShowName" class="form-control showName typeahead"
                data-typeahead-col="LongProgram" placeholder="Show Name" />
              <input type="text" id="filterNetwork" class="form-control network typeahead" data-typeahead-col="Network"
                placeholder="Network" />
              <input type="text" id="filterDvdid" class="form-control DVDID" placeholder="DVD ID #" />
            </div>
            <div class="btn-group" role="group">
              <button type="reset" class="btn btn-danger resetSearch">
                Reset
              </button>
              <button type="submit" class="btn btn-success submitSearch">
                Submit
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="database row">
        <table id="database" class="table table-responsive table-bordered table-hover">
          <thead>
            <tr>
              <th>DVD ID #</th>
              <th>Show Name</th>
              <th>Network</th>
              <th>Date</th>
              <th class="no-sort">Duration</th>
              <th class="no-sort">Time Pos.</th>
              <th class="no-sort">Location</th>
              <th class="no-sort">Operations</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    <!------------------------------ Begin Modals ------------------------------->
  <!-- Add Record Modal -->
    <div id="add-record" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
            <h4 class="modal-title">Add Record</h4>
          </div>
          <form id="form-add-shows" name="addForm" method="post">
            <div class="modal-body form-horizontal">
              <div class="form-group initial">
                <label for="DVDID" class="col-sm-3 control-label">DVD/VHS #</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control addInput" id="DVDID" name="dvdid" />
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-12">
                  <div class="well well-sm">
                    Here you can add multiple shows to the added video record.
                    At minimum, each show requires AirDate, ShowName, and
                    Network are required. This tool will only attempt to add the
                    shows that have all of those fields filled.
                  </div>
                </div>

                <div class="col-sm-12">
                  <div class="panel panel-default panel-show">
                    <div class="panel-heading">
                      <span>Show #<span class="panel-num">1</span></span>
                      <button type="button" class="close pull-right" style="display: none">
                        &times;
                      </button>
                    </div>
                    <div class="panel-body">
                      <div class="form-group">
                        <label for="txtAirDate" class="col-sm-4 control-label">AirDate</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control addInput" name="air_date[]" placeholder="mm/dd/yy" />
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="txtProgramTime" class="col-sm-4 control-label">Program Time</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control addInput" id="txtProgramTime" name="program_time[]"
                            placeholder="hh:mm pm" />
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="txtDuration" class="col-sm-4 control-label">Duration</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control addInput" id="txtDuration" name="duration[]" />
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="txtShowName" class="col-sm-4 control-label">Show Name</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control addInput" id="txtShowName" name="show_name[]" />
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="txtNetwork" class="col-sm-4 control-label">Network</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control addInput" id="txtNetwork" name="network[]" />
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="txtTimePosition" class="col-sm-4 control-label">Time Pos.</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control addInput" id="txtTimePosition" name="time_position[]" />
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="txtLocation" class="col-sm-4 control-label">Location</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control addInput" id="txtLocation" name="location[]" />
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="Comments" class="col-sm-4 control-label">Comments</label>
                        <div class="col-sm-8">
                          <textarea class="form-control addInput" id="txtComments" name="comments[]"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- ./panel -->

                  <div class="clearfix"></div>

                  <button type="button" id="btnAddAnotherShow" class="btn btn-default">
                    Add Another
                  </button>
                </div>
              </div>
              <p class="process text-center processSuccess">
                Record successfully added!
              </p>
              <p class="process text-center processFailure">
                Failed to add specified record.
              </p>
              <p class="process text-center processError"></p>
              <div class="row text-center">
                <button class="btn btn-primary process processClose" data-dismiss="modal">
                  (x) Close
                </button>
              </div>
              <div id="addLoading">
                <div class="warningGradientOuterBarG">
                  <div class="warningGradientFrontBarG warningGradientAnimationG">
                    <div class="warningGradientBarLineG"></div>
                    <div class="warningGradientBarLineG"></div>
                    <div class="warningGradientBarLineG"></div>
                    <div class="warningGradientBarLineG"></div>
                    <div class="warningGradientBarLineG"></div>
                    <div class="warningGradientBarLineG"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <p style="float: left">* Indicates a required field.</p>
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-warning" data-dismiss="modal">
                  Cancel
                </button>
                <button type="reset" class="btn btn-danger button-add-reset" data-dismiss="reset">
                  Reset
                </button>
                <button type="submit" class="btn btn-success add-record-submit" data-dismiss="submit">
                  Submit
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Record Modal -->
    <div id="edit-record" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
            <h4 class="modal-title">Edit Record</h4>
          </div>
          <form name="editForm">
            <div class="modal-body form-horizontal">
              <div class="form-group initial">
                <input class="modalRecordID" type="text" hidden="hidden" value="" />
                <label for="editAirDate" class="col-sm-3 control-label">Air Date *</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control editInput" id="editAirDate" />
                </div>
                <label for="editProgramTime" class="col-sm-3 control-label">Program Time</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control editInput" id="editProgramTime" />
                </div>
                <label for="editDVDID" class="col-sm-3 control-label">DVD ID #</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control editInput" id="editDVDID" />
                </div>
                <label for="editShowName" class="col-sm-3 control-label">Show Name</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control editInput" id="editShowName" />
                </div>
                <label for="editNetwork" class="col-sm-3 control-label">Network</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control editInput" id="editNetwork" />
                </div>
                <label for="editDuration" class="col-sm-3 control-label">Duration</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control editInput" id="editDuration" />
                </div>
                <label for="editTimePosition" class="col-sm-3 control-label">Time Pos.</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control editInput" id="editTimePosition" />
                </div>
                <label for="editLocation" class="col-sm-3 control-label">Location</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control editInput" id="editLocation" />
                </div>
                <label for="editComments" class="col-sm-3 control-label">Comments</label>
                <div class="col-sm-9">
                  <textarea class="form-control editInput" id="editComments"></textarea>
                </div>
              </div>
              <p class="process text-center processSuccess">
                Record successfully edited!
              </p>
              <p class="process text-center processFailure">
                Failed to edit specified record.
              </p>
              <p class="process text-center processError"></p>
              <div class="row text-center">
                <button class="btn btn-primary process processClose" data-dismiss="modal">
                  (x) Close
                </button>
              </div>
              <div id="editLoading">
                <div class="warningGradientOuterBarG">
                  <div class="warningGradientFrontBarG warningGradientAnimationG">
                    <div class="warningGradientBarLineG"></div>
                    <div class="warningGradientBarLineG"></div>
                    <div class="warningGradientBarLineG"></div>
                    <div class="warningGradientBarLineG"></div>
                    <div class="warningGradientBarLineG"></div>
                    <div class="warningGradientBarLineG"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-warning" data-dismiss="modal">
                  Cancel
                </button>
                <button type="reset" class="btn btn-danger button-edit-reset" data-dismiss="reset">
                  Reset
                </button>
                <button type="submit" class="btn btn-success edit-record-submit" data-dismiss="submit">
                  Submit
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Record Modal -->
    <div id="delete-record" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
            <h4 class="modal-title">
              <i class="fa fa-exclamation-triangle" style="color: red"></i>
              Delete Record
            </h4>
          </div>
          <div class="modal-body">
            <input class="modalRecordID" type="text" hidden="hidden" value="" />
            <p class="initial">
              Are you sure you want to delete this record? This cannot be
              undone.
            </p>
            <p class="process text-center processSuccess">
              Record successfully deleted!
            </p>
            <p class="process text-center processFailure">
              Failed to delete specified record.
            </p>
            <p class="process text-center processError"></p>
            <div class="row text-center">
              <button class="btn btn-primary process processClose" data-dismiss="modal">
                (x) Close
              </button>
            </div>
            <div id="deleteLoading">
              <div class="warningGradientOuterBarG">
                <div class="warningGradientFrontBarG warningGradientAnimationG">
                  <div class="warningGradientBarLineG"></div>
                  <div class="warningGradientBarLineG"></div>
                  <div class="warningGradientBarLineG"></div>
                  <div class="warningGradientBarLineG"></div>
                  <div class="warningGradientBarLineG"></div>
                  <div class="warningGradientBarLineG"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-warning" data-dismiss="modal">
                Cancel
              </button>
              <button type="button" class="btn btn-success delete-record-submit" data-dismiss="submit">
                Submit
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!------------------------------- End Modals -------------------------------->
    <script type="application/javascript">
      window.dvr_ajax_object = {
        // ajax_url: "https://apps.mrc.org/dvr/",
        ajax_url: "<?php echo $_ENV['AJAX_URL']; ?>",
      };
    </script>
    <!-- External JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- Local JS -->
    <script src="./js/dvr.js"></script>
    <script src="./js/typeahead.bundle.js"></script>
  <?php endif; ?>
</body>

</html>