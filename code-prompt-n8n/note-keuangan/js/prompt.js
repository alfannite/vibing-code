//Prompt nya bisa kita ganti dengan yang kita mau sendiri
const items = $input.all();
const item = items[0];
 
const binaryData = item.binary.data;
const base64String = binaryData.data;

const geminiPayload = {
  "contents": [
    {
      "parts": [
        {
          "text": "Analisa struk belanja pada gambar ini dan berikan informasi dalam format yang PERSIS seperti ini:\n\nTOKO: [nama toko dari struk]\nTOTAL: [total pembayaran dalam angka saja tanpa Rp]\nITEMS: [daftar barang yang dibeli, pisahkan dengan koma]\nTANGGAL: [tanggal transaksi jika terlihat, atau 2025-06-19]\n\nContoh response yang benar:\nTOKO: Alfamart\nTOTAL: 25500\nITEMS: Susu Ultra 1L, Roti Tawar, Sabun Lifebuoy\nTANGGAL: 2025-06-19\n\nJika struk tidak jelas atau tidak bisa dibaca, berikan:\nERROR: Struk tidak dapat dibaca dengan jelas\n\nPENTING: Hanya berikan response dalam format di atas!"
        },
        {
          "inline_data": {
            "mime_type": "image/jpeg",
            "data": base64String
          }
        }
      ]
    }
  ],
  "generationConfig": {
    "responseMimeType": "text/plain"
  }
};

console.log('Base64 length:', base64String?.length);
console.log('Mime type:', binaryData.mimeType);

return [{
  json: {
    gemini_payload: geminiPayload,
    base64_ready: !!base64String,
    data_length: base64String?.length || 0
  }
}];
