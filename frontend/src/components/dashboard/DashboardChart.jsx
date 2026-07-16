function DashboardChart({ data = {} }) {
  // Gunakan data real dari API jika ada
  const slipTerbaru = Array.isArray(data?.slip_terbaru) ? data.slip_terbaru : [];

  // Kelompokkan slip per bulan untuk grafik
  const bulanMap = {};
  slipTerbaru.forEach((item) => {
    const key = item.bulan || "";
    bulanMap[key] = (bulanMap[key] || 0) + 1;
  });

  const labels = Object.keys(bulanMap);
  const values = Object.values(bulanMap);

  const chartLabels = labels;
  const chartValues = values;

  const maxVal = Math.max(...chartValues, 1);

  return (
    <div className="bg-white rounded-2xl shadow p-6 sm:p-8 h-[380px] min-w-0 overflow-hidden">
      <h2 className="text-xl sm:text-2xl font-bold mb-8 leading-snug">
        Grafik Pembagian Slip Gaji
      </h2>

      {!chartValues.length ? (
        <div className="flex h-64 items-center justify-center rounded-xl border border-dashed border-gray-200 text-gray-500">
          Belum ada data grafik.
        </div>
      ) : (
        <div className="flex items-end justify-around gap-3 h-64 overflow-hidden">

          {chartValues.map((item, index) => (
            <div
              key={index}
              className="flex min-w-0 flex-col items-center"
            >

              <div
                className="w-8 sm:w-10 rounded-t-xl bg-green-500"
                style={{
                  height: `${(item / maxVal) * 100}%`,
                }}
              />

              <span className="mt-3 max-w-20 truncate text-gray-500">
                {chartLabels[index]}
              </span>

            </div>
          ))}

        </div>
      )}
    </div>
  );
}

export default DashboardChart;
