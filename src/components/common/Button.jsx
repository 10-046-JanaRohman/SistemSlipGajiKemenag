function Button({ children, className = "", ...props }) {
  return (
    <button
      {...props}
      className={`
        bg-green-700
        hover:bg-green-800
        text-white
        font-semibold
        rounded-xl
        transition
        duration-300
        ${className}
      `}
    >
      {children}
    </button>
  );
}

export default Button;