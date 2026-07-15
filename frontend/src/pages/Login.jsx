import LoginLeft from "../components/login/LoginLeft";
import LoginRight from "../components/login/LoginRight";

function Login() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-green-50 to-green-200 flex items-center justify-center">

      <div className="bg-white rounded-3xl shadow-2xl w-[1100px] h-[650px] overflow-hidden flex">

        <LoginLeft />

        <LoginRight />

      </div>

    </div>
  );
}

export default Login;