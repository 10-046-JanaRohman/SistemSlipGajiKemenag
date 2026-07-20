function UploadButton({ onClick, disabled = false, loading = false, label = "Import Data" }) {
  return (
    <div className="flex justify-end">

      <button
        type="button"
        onClick={onClick}
        disabled={disabled}
        className="
          bg-green-700
          hover:bg-green-800
          text-white
          px-10
          py-4
          rounded-xl
          font-semibold
          transition
          disabled:cursor-not-allowed
          disabled:opacity-60
        "
      >
        {loading ? "Memproses..." : label}
      </button>

    </div>
  );
}

export default UploadButton;
