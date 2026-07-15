import { useNavigate } from "react-router-dom";

function StatCard({
  title,
  value,
  to = "#",
}) {

  const navigate = useNavigate();

  return (
    <div
      onClick={() => navigate(to)}
      className="
        bg-white
        rounded-2xl
        shadow-sm
        border
        p-6

        cursor-pointer

        transition-all
        duration-300

        hover:-translate-y-1
        hover:shadow-xl
        hover:border-green-600

        active:scale-95
      "
    >

      <p className="text-gray-500 text-lg">

        {title}

      </p>

      <h2 className="text-4xl font-bold text-black mt-3">

        {value}

      </h2>

    </div>
  );
}

export default StatCard;