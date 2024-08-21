{{-- <div class="card shadow-sm graphs">
  <div class="card-header">
    <button class="btn btn-link graph-btn" style="text-decoration:none"><strong>+ Graphs</strong></button>
  </div>
  <div class="card-body" style="display:none">
    <div class="row">
      <div class="col-lg graph">
        <div class="p-3 header">
          <strong>Total Reg, Unreg, Purged &amp; Net New Sub</strong>
        </div>
        <canvas id="mixedCanvas" height="0" class="chartjs-render-monitor" style="display: block; width: 0px; height: 0px;" width="0"></canvas>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-8 graph">
        <div class="p-3 header">
          <strong>Revenue</strong>
        </div>
        <canvas id="revCanvas" height="0" class="chartjs-render-monitor" style="display: block; width: 0px; height: 0px;" width="0"></canvas>
      </div>
      <div class="col-lg-8 graph">
        <div class="p-3 header">
          <strong>Total Subactive, Average Subactive &amp; Renewal</strong>
        </div>
        <canvas id="subCanvas" height="0" class="chartjs-render-monitor" style="display: block; width: 0px; height: 0px;" width="0"></canvas>
      </div>
    </div>
  </div>
</div> --}}

<div class="card shadow-sm graphs">
  <div class="card-header">
    <button class="btn btn-link graph-btn" style="text-decoration:none"><strong>+ Graphs</strong></button>
  </div>
  <div class="card-body" style="display:none">
    <div class="row">
      <div class="col-lg graph">
        <div class="p-3 header">
          <strong>Total Reg, Unreg, Purged & Net New Sub</strong>
        </div>
        <canvas id="mixedCanvas"></canvas>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-8 graph">
        <div class="p-3 header">
          <strong>Revenue</strong>
        </div>
        <canvas id="revCanvas"></canvas>
      </div>
      <div class="col-lg-8 graph">
        <div class="p-3 header">
          <strong>Total Subactive, Average Subactive & Renewal</strong>
        </div>
        <canvas id="subCanvas"></canvas>
      </div>
    </div>
  </div>
</div>