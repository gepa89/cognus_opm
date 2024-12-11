<!-- Modal -->
<div class="modal fade bd-example-modal-lg"  id="modalMatcBox" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
              </div>
              <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12">
                            <div class="input-group">
                              <input class="form-control" value="" id="strin" name="strin" />
                              <span class="input-group-btn">
                                  <button class="btn btn-default" onclick="searchMatchModal()" type="button"><span class="glyphicon glyphicon-search"></span></button>
                              </span>
                              <input type="hidden" class="form-control" value="" id="column" name="column" />
                              <input type="hidden" class="form-control" value="" id="table" name="table" />
                              <input type="hidden" class="form-control" value="" id="limAr" name="limAr" />
                            </div><!-- /input-group -->
                        </div>
                    </div><br/><br/>
                    <div id="row">
                        <h2 id="txt-dir"></h2>
                        <div id="resultGrid"></div>
                    </div>
                    
                    <div style="clear:both;"></div>
              </div>
        </div>

    </div>
</div>
<div style="clear:both;"></div>













