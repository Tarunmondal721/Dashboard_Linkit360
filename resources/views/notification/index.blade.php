<form action="{{route('add.notification.deployment')}}" method="post" onsubmit="return notificationSubmit()">
    @csrf
      
    <div class="card shadow-sm mt-0">
      <div class="card-body">
          
        <div class="row">

              <div class="col-lg-6" style="padding-top: 10px;">
                  <label>Country*</label>
                  <select class="form-control select2" id="country_name" name="country" onchange="countryChange()">
                      <option value="">Select Country</option>
                      @foreach ($countrys as $country)
                          <option value="{{$country->country_id}}">{{$country->country_name}}</option>
                      @endforeach
                  </select>
                  <span class="gu-hide" style="color: red;"
                      id="errorcountry">{{ __('*Please select country') }}</span>
              </div>
              <div class="col-lg-6" style="padding-top: 10px;">
                  <label>Operator*</label>
                  <select class="form-control select2" id="operator_name" name="operator">
                      <option value="">Select Operator</option>
                      
                  </select>
                  <span class="gu-hide" style="color: red;"
                      id="erroroperator">{{ __('*Please select Operator') }}</span>
              </div>
              <div class="col-lg-6" style="padding-top: 10px;">
                  <label>Category</label>
                  <select class="form-control select2" id="category" name="category">
                      <option value="">Select Category</option>
                      <option value="deployment">Deployment</option>
                      <option value="bug">Bug</option>
                      <option value="incident">Incident</option>
                     
                  </select>
              </div>
              <div class="col-lg-6" style="padding-top: 10px;">
                <div class="form-group">
                  <label>Title</label>
                    <input type="text" class="form-control select2" id="title"
                    name="title" placeholder="Input title">
                  <span class="gu-hide" style="color: red;"
                  id="errorTitle">{{ __('*Please input title') }}</span>
                      
                </div>
              </div>
              <div class="col-lg-6" style="padding-top: 10px;">
                <div class="form-group">
                  <label>Message</label>
                    <input type="text" class="form-control select2" id="message"
                    name="message" placeholder="Input message">
                  <span class="gu-hide" style="color: red;"
                  id="errorMessage">{{ __('*Please input message') }}</span>

                </div>
              </div>
              <div class="col-lg-6" style="padding-top: 10px;">
                <div class="form-group">
                  <label>Web Portal/Platform Name</label>
                    <input type="text" class="form-control select2" id="web_portal"
                    name="web_portal" placeholder="Input platform">
                  <span class="gu-hide" style="color: red;"
                  id="errorWebPortal">{{ __('*Please input platform') }}</span>

                </div>
              </div>
              <div class="col-lg-6" style="padding-top: 10px;">
                <div class="form-group">
                  <label>Status</label>
                    <input type="text" class="form-control select2" id="status"
                    name="status" placeholder="Input status">
                  <span class="gu-hide" style="color: red;"
                  id="errorStatus">{{ __('*Please input status') }}</span>

                </div>
              </div>
              <div class="col-lg-6" style="padding-top: 10px;">
                <div class="form-group">
                  <label>Activity Time</label>
                  <input class="form-control time-activity"  id="time_activity" name="time_activity" type="text" style="height: 40px;">
                  <span class="gu-hide" style="color: red;"
                  id="errorActivity">{{ __('*Please input activity time') }}</span>

                </div>
              </div>
              <div class="col-lg-12" style="padding-top: 10px;">
                <div class="form-group">
                  <label>Deployment Details</label>
                  <textarea id="deployment_details"  class="form-control"  name="deployment_details"></textarea>
                  <span class="gu-hide" style="color: red;"
                  id="errorDeploymentDetails">{{ __('*Please input details deployment') }}</span>

                </div>
              </div>
          </div>
          <div class="row">

              <div class="col-md-12" style="text-align:right;">

                  <button type="submit" class="btn badge-blue">Create</button>
              </div>

          </div>
      </div>
  </div>
  </div>
</form>