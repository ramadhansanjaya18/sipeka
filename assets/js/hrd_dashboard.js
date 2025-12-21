$(document).ready(function () {
  const chartDataEl = document.getElementById("dashboardChartData");
  if (chartDataEl) {
    if ($("#chartPelamarPerLowongan").length) {
      const barLabels = JSON.parse(chartDataEl.dataset.barLabels);
      const barData = JSON.parse(chartDataEl.dataset.barData);
      const style = getComputedStyle(document.documentElement);
      const barColor = style.getPropertyValue("--side-bg") || "#6A4E3B";
      const barBorder = style.getPropertyValue("--dark-brown") || "#4A3B32";
      const ctxBar = document
        .getElementById("chartPelamarPerLowongan")
        .getContext("2d");
      new Chart(ctxBar, {
        type: "bar",
        data: {
          labels: barLabels,
          datasets: [
            {
              label: "Jumlah Pelamar",
              data: barData,
              backgroundColor: barColor,
              borderColor: barBorder,
              borderWidth: 1.5,
              borderRadius: 4,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
          },
          scales: {
            y: { beginAtZero: true },
            x: { grid: { display: false } },
          },
        },
      });
    }

    if ($("#pieChartContainer").length) {
      const pieData = JSON.parse(chartDataEl.dataset.pieData);
      const textColor =
        getComputedStyle(document.documentElement).getPropertyValue(
          "--text-primary"
        ) || "#333";

      var chart = new CanvasJS.Chart("pieChartContainer", {
        animationEnabled: true,
        backgroundColor: "transparent",
        data: [
          {
            type: "pie",
            radius: "85%",
            indexLabelFontSize: 12,
            indexLabelFontColor: textColor,
            indexLabel: "{label} - {y}",
            yValueFormatString: '#,##0" pelamar"',
            toolTipContent: "{label}: <strong>{y}</strong> pelamar",
            dataPoints: pieData,
          },
        ],
      });
      chart.render();
    }
  }
});
