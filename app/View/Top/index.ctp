<!DOCTYPE html>
<html>
<head>
  <title>Mimamori Kun</title>
  <link rel="stylesheet" href="lib/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/keen-dashboards.css">
  <link rel="stylesheet" href="lib/epoch/epoch.css">
  <meta charset="UTF-8">
</head>
<body class="application">

  <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="top">Mimamori Kun</a>
      </div>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-8">
        <div class="chart-wrapper">
          <div class="chart-title">
            Real Time Expression Report
          </div>
          <div class="chart-stage">
            <div id="areaChart" class="epoch" style="width: auto; height: 222px;"></div>
            <div style="text-align:center;">
              <span style="color: #1F77B4; font-size: 20px;">■</span><span style="font-size: 12px"> NeutralL</span>
              <span style="color: #FF7F0E; font-size: 20px;">■</span><span style="font-size: 12px"> Happiness</span>
              <span style="color: #2CA02C; font-size: 20px;">■</span><span style="font-size: 12px"> Surprise</span>
              <span style="color: #D62728; font-size: 20px;">■</span><span style="font-size: 12px"> Anger</span>
              <span style="color: #9467BD; font-size: 20px;">■</span><span style="font-size: 12px"> Sadness</span>
            </div>
          </div>
          <div class="chart-notes">
            This graph refreshs every 5 seconds.
          </div>
        </div>
      </div>
    </div>


    <div class="row">
      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Positive Negative Report
          </div>
          <div class="chart-stage">
            <div id="pie" class="epoch"  style="width: 222px; height: 222px; text-align: center;"></div>
            <div style="text-align:center;">
                <span style="color: #1F77B4; font-size: 20px;">■</span><span style="font-size: 12px"> Negative</span>
                <span style="color: #FF7F0E; font-size: 20px;">■</span><span style="font-size: 12px"> Positive</span>
            </div>
          </div>
          <div class="chart-notes">
              This graph refreshs every 5 seconds.
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="chart-wrapper">
          <div class="chart-title">
            Negative Meter
          </div>
          <div class="chart-stage">
            <div id="gaugeChart" class="epoch gauge-small" style="width: 50; height: 250px; text-align: center;"></div>
          </div>
          <div class="chart-notes">
            This graph refreshs every 5 seconds.
          </div>
        </div>
      </div>
    </div>
    <hr>
    <p class="small text-muted">Built by Hackathon Boot Camp</p>
  </div>

  <script src="lib/jquery/dist/jquery.min.js"></script>
  <script src="lib/bootstrap/dist/js/bootstrap.min.js"></script>
  <script src="lib/epoch/d3.js"></script>
  <script src="lib/epoch/epoch.js"></script>
  <script src="lib/periodicalupdater/jquery.periodicalupdater.js"></script>
  <script>
      idx = ((new Date()).getTime()/1000)|0;
      var areaData = [
          { label: 'Layer 1', values: [ {time: idx, y: 0}] },  //NEUTRAL #1F77B4
          { label: 'Layer 2', values: [ {time: idx, y: 0}] },  //HAPPINESS #FF7F0E
          { label: 'Layer 3', values: [ {time: idx, y: 0}] },  //SURPRISE #2CA02C
          { label: 'Layer 4', values: [ {time: idx, y: 0}] },  //ANGER #D62728
          { label: 'Layer 5', values: [ {time: idx, y: 0}] }   //SADNESS #9467BD
      ];

      var areaChartInstance = $('#areaChart').epoch({
          type: 'time.area',
//          type: 'time.bar',
//          type: 'time.line',
          data: areaData,
          axes: ['right', 'bottom', 'left'],
          fps: 60,
          pixelRatio: 1
      });

      var nextData = [];
      $(document).ready(function () {
          $.PeriodicalUpdater('expression/readExpressionReport', {
                  method: 'post',
                  minTimeout: 5000,
                  autoStop: 0,
                  maxCalls: 0,
                  runatonce: true
              },
              function (data) {
                  var nextAreaData = [];
                  var res = $.parseJSON(data);
                  for(var code in res){
                      var now = res[code]['now'];
                      var score = res[code]['score'];
                      // var degree = res[code]['degree'];
                      var str = '{"time": '+ now + ', "y": ' + score + '}';
                      var mJson = $.parseJSON(str);
                      nextAreaData.push(mJson);
                  }
                  areaChartInstance.push(nextAreaData);
              });
      })
  </script>
  <script>
      $(document).ready(function () {
          var pieData = [
              { label: 'Negative', value: 0 },
              { label: 'Positive', value: 0 }
          ]
          var pieInstance = $('#pie').epoch({
              type: 'pie',
              data: pieData,
              height:220,
              width: 220
          });

          var gaugeInstance = $('#gaugeChart').epoch({
              type: 'time.gauge',
              fps: 60,
              pixelRatio: 1,
              value: 0.4,
              height: 180,
              width: 240
          });

          $.PeriodicalUpdater('expression/readDegreeReport', {
                  method: 'post',
                  minTimeout: 5000,
                  autoStop: 0,
                  maxCalls: 0,
                  runatonce: true
              },
              function (data) {
                  var res = $.parseJSON(data);
                  var positiveParcent = res['positiveParcent'];
                  var negativeParcent = res['negativeParcent'];

                  //pie graph refresh
                  var nextPieData = [
                      { label: 'Negative', value: negativeParcent },
                      { label: 'Positive', value: positiveParcent }
                  ]
                  pieInstance.update(nextPieData);

                  //gauge graph refresh
                  gaugeInstance.push((negativeParcent / 100));

              });
      })
  </script>
</body>
</html>
