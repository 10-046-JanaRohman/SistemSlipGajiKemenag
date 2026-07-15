function UploadButton() {
  return (
    <div className="flex justify-end">

      <button
        className="
          bg-green-700
          hover:bg-green-800
          text-white
          px-10
          py-4
          rounded-xl
          font-semibold
          transition
        "
      >
        Import Data
      </button>

    </div>
  );
}

export default UploadButton;