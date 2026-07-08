import { Search } from "lucide-react";

function SearchBox({
  placeholder = "Cari...",
}) {
  return (
    <div className="relative w-[420px]">

      <Search
        size={20}
        className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
      />

      <input
        type="text"
        placeholder={placeholder}
        className="w-full h-14 border rounded-xl pl-12 pr-4 outline-none focus:ring-2 focus:ring-green-700"
      />

    </div>
  );
}

export default SearchBox;