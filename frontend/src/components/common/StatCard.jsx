function StatCard({
    title,
    value,
    color = "text-green-700",
}) {
    return (
        <div className="bg-white rounded-2xl shadow-md p-6 hover:shadow-lg transition">

            <p className="text-gray-500 text-lg">
                {title}
            </p>

            <h2 className={`text-5xl font-bold mt-3 ${color}`}>
                {value}
            </h2>

        </div>
    );
}

export default StatCard;