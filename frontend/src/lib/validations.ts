import { z } from "zod";

// Schema validasi pendaftaran peserta (Hybrid)
export const participantSchema = z.object({
  name: z.string().min(3, "Nama lengkap minimal 3 karakter"),
  gender: z.enum(["PUTRA", "PUTRI", "MIXED"]),
  birthDate: z.string().min(1, "Tanggal lahir wajib diisi"),
  email: z.string().email("Format email tidak valid").optional().or(z.literal("")),
  phone: z.string().min(8, "Nomor telepon minimal 8 digit").optional().or(z.literal("")),
  contingentId: z.string().min(1, "Kontingen wajib dipilih"),
  divisionId: z.string().min(1, "Kelas pertandingan wajib dipilih"),
});

export type ParticipantFormValues = z.infer<typeof participantSchema>;
