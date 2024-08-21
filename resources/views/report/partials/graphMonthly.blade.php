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
        <canvas id="mixedCanvas-monthly"></canvas>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-8 graph">
        <div class="p-3 header">
          <strong>Revenue</strong>
        </div>
        <canvas id="revCanvas-monthly"></canvas>
      </div>
      <div class="col-lg-8 graph">
        <div class="p-3 header">
          <strong>Total Subactive, Average Subactive & Renewal</strong>
        </div>
        <canvas id="subCanvas-monthly"></canvas>
      </div>
    </div>
  </div>
</div>