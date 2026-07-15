import UserLayout from "../../layouts/UserLayout";
import PageTransition from "../../components/common/PageTransition";

import UserProfileHeader from "../../components/user/UserProfileHeader";
import UserProfileInfo from "../../components/user/UserProfileInfo";
import UserProfileSecurity from "../../components/user/UserProfileSecurity";

function Profil() {
  return (
    <UserLayout>
      <PageTransition>

        <div className="space-y-8">

          <UserProfileHeader />

          <UserProfileInfo />

          <UserProfileSecurity />

        </div>

      </PageTransition>
    </UserLayout>
  );
}

export default Profil;