function StatCard({ title, value, color }) {
  return (
    <div className="bg-white rounded-2xl shadow-sm p-6 border">

      <p className="text-gray-500">
        {title}
      </p>

      <h2
        className={`text-4xl font-bold mt-3 ${color}`}
      >
        {value}
      </h2>

    </div>
  );
}

export default StatCard;