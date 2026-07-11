import type { Metadata } from "next";
import { getServerSession } from "next-auth";
import { redirect } from "next/navigation";
import { authOptions } from "@/lib/auth";
import { LoginForm } from "@/components/auth/login-form";

export const metadata: Metadata = {
  title: "Login Admin",
};

export default async function LoginPage() {
  const session = await getServerSession(authOptions);
  if (session) redirect("/admin");

  return (
    <div className="flex min-h-screen items-center justify-center bg-secondary/30 px-4">
      <div className="w-full max-w-sm">
        <div className="mb-8 text-center">
          <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-md bg-primary font-serif text-xl font-bold text-primary-foreground">
            S
          </div>
          <h1 className="font-serif text-2xl font-semibold tracking-tight">
            EO Sanasini
          </h1>
          <p className="mt-1 text-sm text-muted-foreground">
            Login Panel Admin
          </p>
        </div>
        <LoginForm />
      </div>
    </div>
  );
}
