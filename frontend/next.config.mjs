/** @type {import('next').NextConfig} */
const nextConfig = {
  output: "standalone",
  // allow image dari domain eksternal (poster event, logo kontingen, dll)
  images: {
    remotePatterns: [
      { protocol: "https", hostname: "**" },
    ],
  },
};

export default nextConfig;
