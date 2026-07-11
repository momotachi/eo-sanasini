import type { Metadata } from "next";
import { Inter, Playfair_Display } from "next/font/google";
import "./globals.css";
import { Navbar } from "@/components/layout/navbar";
import { Footer } from "@/components/layout/footer";

const sans = Inter({
  subsets: ["latin"],
  variable: "--font-sans",
  display: "swap",
});

const serif = Playfair_Display({
  subsets: ["latin"],
  variable: "--font-serif",
  display: "swap",
});

export const metadata: Metadata = {
  title: {
    default: "EO Sanasini — Event Organizer, MICE & Travel Agency",
    template: "%s — EO Sanasini",
  },
  description:
    "Event Organizer, MICE & Travel Agency berpengalaman sejak 2009. Menyelenggarakan kejuaraan olahraga, festival, dan konferensi berskala nasional.",
  openGraph: {
    title: "EO Sanasini",
    description:
      "Event Organizer, MICE & Travel Agency berpengalaman sejak 2009.",
    type: "website",
  },
};

export default function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="id" className={`${sans.variable} ${serif.variable}`}>
      <body className="min-h-screen font-sans antialiased">
        <Navbar />
        <main>{children}</main>
        <Footer />
      </body>
    </html>
  );
}
