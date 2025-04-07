<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<title>Cetak AWB</title>

<style>
  body {
    font-family: sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;

  }
  .page-container {
            width: 210mm; /* A4 width */
            height: 297mm; /* A4 height */
            display: flex;
            flex-wrap: wrap;
            padding:5mm;
        }
.label-container {
            width: 95mm; /* 1/2 dari kertas A4 (210mm / 2 - margin) */
            height: 138.5mm; /* 1/2 dari kertas A4 (297mm / 2 - margin) */
            border: 2px solid black;
            padding: 2mm;
            font-family: roboto, sans-serif;
            margin: 1mm;
            box-sizing: border-box;
        }

  .header {
    text-align: left;
    margin-bottom: 10px;
  }
  .barcode {
    text-align: right;
    margin-top: 5px;
  }
  .awb-number {
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 15px;
  }
  .package-info, .service-info, .payment-info {
    margin-bottom: 8px;
  }
  .sender-recipient {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
  }
  .sender, .recipient {
    width: 48%;
  }
  .qr-code {
    text-align: center;
    margin-top: 10px;
  }
  .label {
    font-weight: bold;
  }

  .divider {
            border-top: 1px solid black;
            margin:1px 0;
        }

        .medium-text {
            font-size: 11px;
        }

  .logo-text {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-text img {
            height: 60px;
            margin-right: 5px;
        }
</style>
</head>

<body>
    <div class="page-container">
        @for ($i = 0; $i < 4; $i++)
        <div class="label-container">
            <div class="header">
                <div class="logo-text" style="text-align: center;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" alt="Logo">
                </div>
                <div class="divider"></div>
                <div>
                    <p class="medium-text" style="text-align: center;">Jl.Sei Miai Dalam RT.12 Pondok Kelapa 4 no.1 Banjarmasin</p>
                </div>
            </div>
            <div class="barcode">
                <div class="barcode" style="text-align: center;">
                   c
                    <div class="info bold" style="text-align: center; font-size: 12px;">
                        <div class="awb-number">
                            AWB {{ $resiumum->noresi }}
                        </div>
                        <div class="package-info">
                            <span class="label">PACKAGE REFERENCE:</span><br>
                            Date:{{ $resiumum->date_input }}  | Amount: {{ $resiumum->koli }} | Weight :
                            @php
                            $effectiveWeight = max(
                                floatval(str_replace(['.', ','], ['', '.'], $resiumum->weight)),
                                floatval(str_replace(['.', ','], ['', '.'], $resiumum->total_weight_volume))
                            );
                            @endphp
                             {{ $effectiveWeight }} kg  | Rp.{{ $resiumum->total }} <br>

                        </div>
                        <div class="service-info">
                            <span class="label">SERVICE REFERENCE:</span> {{ $resiumum->jenis_kiriman }}| Hajiboy Delivery
                        </div>
                        <div class="payment-info">
                            <span class="label">Payment status:</span>{{ $resiumum->jenis_pembayaran }}

                             <div class="qr-code">
                                <div style="margin: 5px 0;">
                                    {!! QrCode::size(60)->generate($resiumum->noresi) !!}
                                </div>
                            </div>


                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    for (var i = 0; i < 4; i++) {
                                        var qr = qrcode(0, 'M');
                                        qr.addData('{{ $resiumum->noresi }}');
                                        qr.make();
                                        document.getElementById('qrcode-' + i).innerHTML = qr.createImgTag(4);
                                    }
                                });
                            </script>

                        </div>
                        <div class="info bold" style="text-align: center; font-size: 13px;">
                            <span class="label">{{ $resiumum->regency->name ?? $resiumum->orig_regency_id }} - {{ $resiumum->regencydest->name ?? $resiumum->regency_id }}</span>
                        </div>
                        <div class="sender-recipient" style="display: flex; justify-content: space-between;">
                            <div class="sender" style="width: 45%;">
                                <span class="label">Sender</span><br>
                                {{ $resiumum->nama_pengirim }}<br>
                                {{ $resiumum->alamat_pengirim }} <br>
                                {{ $resiumum->telp_pengirim }}
                            </div>
                            <div class="recipient" style="width: 45%;">
                                <span class="label">Recipient</span><br>
                                {{ $resiumum->nama_penerima }} <br>
                                {{ $resiumum->alamat_penerima }} <br>
                                {{ $resiumum->telp_penerima }}<br>
                                {{ $resiumum->kode_pos }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
