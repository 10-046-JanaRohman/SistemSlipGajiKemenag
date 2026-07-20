import LoginLeft from "../components/login/LoginLeft";
import LoginRight from "../components/login/LoginRight";

function Login() {
  return (
    <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-green-50 to-green-200 p-0 sm:p-6">

      <div className="flex min-h-screen w-full max-w-[1100px] overflow-hidden bg-white shadow-2xl sm:min-h-0 sm:rounded-3xl md:min-h-[650px]">

        <LoginLeft />

        <LoginRight />

      </div>

    </div>
  );
}

export default Login;
