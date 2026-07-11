export { default } from "next-auth/middleware";

export const config = {
  // semua admin route butuh login
  matcher: ["/admin/:path*"],
};
