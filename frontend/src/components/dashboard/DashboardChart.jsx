function DashboardChart() {
  return (
    <div className="bg-white rounded-2xl shadow p-8 h-[380px]">
      <h2 className="text-2xl font-bold mb-8">
        Grafik Pembagian Slip Gaji
      </h2>

      <div className="flex items-end justify-around h-64">

        {[40,60,50,70,85,100].map((item,index)=>(
          <div
            key={index}
            className="flex flex-col items-center"
          >

            <div
              className="w-10 rounded-t-xl bg-green-500"
              style={{
                height:`${item}%`
              }}
            />

            <span className="mt-3 text-gray-500">
              {["Jan","Feb","Mar","Apr","Mei","Jun"][index]}
            </span>

          </div>
        ))}

      </div>
    </div>
  );
}

export default DashboardChart;