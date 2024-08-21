<div class="card shadow-sm mt-0">
  <div class="card-body">
    <div class="row">
      <div class="col-md-4">
        <ul class="workflow-list">
          @php
              $count=0;
          @endphp
          @if (isset($progressReports))
          @foreach ($progressReports as $progressReport )
          <li>
            <a href="#" data-url="{{route('report.progress.create',$progressReport->id_service)}}" data-size="lg" data-ajax-popup="true" data-title="{{__('Update Display Workflow')}}" data-toggle="tooltip" data-original-title="Edit Workflow">
              <div class="liContent-wrap">
                <div class="float-left">
                  <div class="progress-box {{isset($progressReport)?$progressReport->status:''}}-bg-color">
                    <div class="white-cercle">{{++$count}}</div>
                   @php
                       if(isset($progressReport) && $progressReport->status == 'in-progress'){
                        $progressReport->status='In Progress';
                       }
                   @endphp
                    <div class="progress-message-text">{{isset($progressReport)?$progressReport->status:''}}</div>
                  </div>
                </div>
                <div class="float-left">
                  <div class="progressBox-textBox">
                    <div class="progressBox-heading"><b>{{isset($progressReport)?$progressReport->serviceStatus->name:''}}</b>
                    </div>
                    <div class="progressBox-date">Due Date - <span>{{isset($progressReport)?$progressReport->dute_date:''}}</span></div>
                  </div>
                </div>
                <div class="clearfix"></div>
              </div>
            </a>
            <div class="progress-linkLine-block">
              @if ($count != count($progressReports))
              <div class="progress-linkLine">&nbsp;</div>
              @endif

            </div>
          </li>
          @if($count%10 == 0)
          </ul>
          </div>
          <div class="col-md-4">
          <ul class="workflow-list">
          @endif
          @endforeach
          @endif

      </ul>
      </div>
          <!-- <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box delay-bg-color">
                  <div class="white-cercle">2</div>
                  <div class="progress-message-text">Delay</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Service Detail Created</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box blocked-bg-color">
                  <div class="white-cercle">3</div>
                  <div class="progress-message-text">Blocked</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Renewal</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box on-progress-bg-color">
                  <div class="white-cercle">4</div>
                  <div class="progress-message-text">On Progress</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Subs and Unsub from Portal and Landing Pag</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box pending-bg-color">
                  <div class="white-cercle">4</div>
                  <div class="progress-message-text">Pending</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Renewal</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box complete-bg-color">
                  <div class="white-cercle">1</div>
                  <div class="progress-message-text">Complete</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Simulate Telco Api [Sub, Unsubs, Renewal, MT]</b>
                  </div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box delay-bg-color">
                  <div class="white-cercle">2</div>
                  <div class="progress-message-text">Delay</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Service Detail Created</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box blocked-bg-color">
                  <div class="white-cercle">3</div>
                  <div class="progress-message-text">Blocked</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Renewal</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box on-progress-bg-color">
                  <div class="white-cercle">4</div>
                  <div class="progress-message-text">On Progress</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Subs and Unsub from Portal and Landing Pag</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box pending-bg-color">
                  <div class="white-cercle">4</div>
                  <div class="progress-message-text">Pending</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Renewal</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>

        </ul>
      </div>
      <div class="col-md-4">
        <ul>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box complete-bg-color">
                  <div class="white-cercle">1</div>
                  <div class="progress-message-text">Complete</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Simulate Telco Api [Sub, Unsubs, Renewal, MT]</b>
                  </div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box delay-bg-color">
                  <div class="white-cercle">2</div>
                  <div class="progress-message-text">Delay</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Service Detail Created</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box blocked-bg-color">
                  <div class="white-cercle">3</div>
                  <div class="progress-message-text">Blocked</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Renewal</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box on-progress-bg-color">
                  <div class="white-cercle">4</div>
                  <div class="progress-message-text">On Progress</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Subs and Unsub from Portal and Landing Pag</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box pending-bg-color">
                  <div class="white-cercle">4</div>
                  <div class="progress-message-text">Pending</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Renewal</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box complete-bg-color">
                  <div class="white-cercle">1</div>
                  <div class="progress-message-text">Complete</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Simulate Telco Api [Sub, Unsubs, Renewal, MT]</b>
                  </div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box delay-bg-color">
                  <div class="white-cercle">2</div>
                  <div class="progress-message-text">Delay</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Service Detail Created</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box blocked-bg-color">
                  <div class="white-cercle">3</div>
                  <div class="progress-message-text">Blocked</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Renewal</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box on-progress-bg-color">
                  <div class="white-cercle">4</div>
                  <div class="progress-message-text">On Progress</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Subs and Unsub from Portal and Landing Pag</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box pending-bg-color">
                  <div class="white-cercle">4</div>
                  <div class="progress-message-text">Pending</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Renewal</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>

        </ul>
      </div>
      <div class="col-md-4">
        <ul>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box complete-bg-color">
                  <div class="white-cercle">1</div>
                  <div class="progress-message-text">Complete</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Simulate Telco Api [Sub, Unsubs, Renewal, MT]</b>
                  </div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box delay-bg-color">
                  <div class="white-cercle">2</div>
                  <div class="progress-message-text">Delay</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Service Detail Created</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box blocked-bg-color">
                  <div class="white-cercle">3</div>
                  <div class="progress-message-text">Blocked</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Renewal</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box on-progress-bg-color">
                  <div class="white-cercle">4</div>
                  <div class="progress-message-text">On Progress</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Subs and Unsub from Portal and Landing Pag</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box pending-bg-color">
                  <div class="white-cercle">4</div>
                  <div class="progress-message-text">Pending</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Renewal</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box complete-bg-color">
                  <div class="white-cercle">1</div>
                  <div class="progress-message-text">Complete</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Simulate Telco Api [Sub, Unsubs, Renewal, MT]</b>
                  </div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box delay-bg-color">
                  <div class="white-cercle">2</div>
                  <div class="progress-message-text">Delay</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Service Detail Created</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box blocked-bg-color">
                  <div class="white-cercle">3</div>
                  <div class="progress-message-text">Blocked</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Renewal</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box on-progress-bg-color">
                  <div class="white-cercle">4</div>
                  <div class="progress-message-text">On Progress</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Subs and Unsub from Portal and Landing Pag</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
          <li>
            <div class="liContent-wrap">
              <div class="float-left">
                <div class="progress-box pending-bg-color">
                  <div class="white-cercle">4</div>
                  <div class="progress-message-text">Pending</div>
                </div>
              </div>
              <div class="float-left">
                <div class="progressBox-textBox">
                  <div class="progressBox-heading"><b>Test Renewal</b></div>
                  <div class="progressBox-date">Due Date - <span>01 / 01 / 2023</span></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="progress-linkLine-block">
              <div class="progress-linkLine">&nbsp;</div>
            </div>
          </li>
        </ul>
      </div> -->
    </div>
  </div>
</div>
