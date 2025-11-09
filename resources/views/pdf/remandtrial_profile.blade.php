<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remand Prisoner Profile - Print</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @media print {
            body {
                margin: 0;
                padding: 20px;
            }

            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
            color: #000;
            line-height: 1.6;
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            border-bottom: 4px solid #000;
            padding-bottom: 20px;
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .station-name {
            font-size: 14px;
            font-weight: 400;
            color: #333;
            margin-top: 5px;
        }

        .section {
            margin-bottom: 35px;
            break-inside: avoid;
        }

        .section-header {
            background: #000;
            color: #fff;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px 20px;
        }

        .grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .field {
            break-inside: avoid;
        }

        .field-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000;
            margin-bottom: 6px;
        }

        .field-value {
            font-size: 14px;
            color: #333;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .photo-section {
            grid-column: 1;
            grid-row: 1 / 3;
        }

        .photo-container {
            width: 150px;
            height: 150px;
            border: 3px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            margin-top: 10px;
        }

        .photo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            background: #000;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 10px;
        }

        .badge-danger {
            background: #dc3545;
        }

        .badge-warning {
            background: #ffc107;
            color: #000;
        }

        .no-data {
            font-style: italic;
            color: #999;
            padding: 20px;
            text-align: center;
            border: 1px dashed #ccc;
        }

        @media print {
            body {
                padding: 20px;
            }

            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div>
            <div class="header-title">{{ $record->full_name }}'s Records</div>
            <div class="station-name">{{ $record->station->name ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- Personal Record Section -->
    <div class="section">
        <div class="section-header">Personal Record</div>
        <div class="grid">
            <div class="field photo-section">
                <div class="field-label">Prisoner Photo</div>
                <div class="photo-container">
                    @if($record->picture && Storage::disk('public')->exists($record->picture))
                        <img src="{{ asset('storage/' . $record->picture) }}" 
                             alt="{{ $record->full_name }}'s Photo"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div class="placeholder" style="display:none;">No photo available</div>
                    @else
                        <div class="placeholder">No photo available</div> 
                    @endif
                </div>
            </div>
            <div class="field">
                <div class="field-label">Serial Number</div>
                <div class="field-value">{{ $record->serial_number }}</div>
            </div>
            <div class="field">
                <div class="field-label">Name of Prisoner</div>
                <div class="field-value">{{ $record->full_name }}</div>
            </div>
            <div class="field">
                <div class="field-label">Gender</div>
                <div class="field-value">{{ $record->gender }}</div>
            </div>
            <div class="field">
                <div class="field-label">Age on Admission</div>
                <div class="field-value">{{ $record->age_on_admission ?? 'N/A' }} {{ $record->age_on_admission ? 'years' : '' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Offence</div>
                <div class="field-value">{{ $record->offense }}</div>
            </div>
            <div class="field">
                <div class="field-label">Date of Admission</div>
                <div class="field-value">{{ $record->admission_date ? \Carbon\Carbon::parse($record->admission_date)->format('j F Y') : 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Court</div>
                <div class="field-value">{{ $record->court }}</div>
            </div>
            <div class="field">
                <div class="field-label">Detention Type</div>
                <div class="field-value">{{ $record->detention_type ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Next Court Date</div>
                <div class="field-value">{{ $record->next_court_date ? \Carbon\Carbon::parse($record->next_court_date)->format('j F Y') : 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Block & Cell</div>
                <div class="field-value">{{ $record->cell_id }}</div>
            </div>
            <div class="field">
                <div class="field-label">Country of Origin</div>
                <div class="field-value">{{ $record->country_of_origin }}</div>
            </div>
        </div>
    </div>

    <!-- Warrant Information -->
    <div class="section">
        <div class="section-header">Warrant Information</div>
        <div class="grid grid-2">
            <div class="field">
                <div class="field-label">Warrant Status</div>
                <div class="field-value">
                    {{ $record->warrant ? 'Available' : 'Not Available' }}
                    @if($record->warrant)
                        <span class="badge">Warrant on File</span>
                    @endif
                </div>
            </div>
            @if($record->warrant)
                <div class="field">
                    <div class="field-label">Warrant Details</div>
                    <div class="field-value">{{ $record->warrant }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Police Information Section -->
    <div class="section">
        <div class="section-header">Police Information</div>
        <div class="grid">
            <div class="field">
                <div class="field-label">Police Officer Name</div>
                <div class="field-value">{{ $record->police_officer ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Police Station</div>
                <div class="field-value">{{ $record->police_station ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Police Contact</div>
                <div class="field-value">{{ $record->police_contact ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- Re-admission Information -->
    @if($record->re_admission_date)
    <div class="section">
        <div class="section-header">Re-admission Information</div>
        <div class="grid grid-2">
            <div class="field">
                <div class="field-label">Re-admission Status</div>
                <div class="field-value">
                    Yes
                    <span class="badge badge-warning">Re-admitted</span>
                </div>
            </div>
            <div class="field">
                <div class="field-label">Re-admission Date</div>
                <div class="field-value">{{ $record->re_admission_date ?? \Carbon\Carbon::parse($record->re_admission_date)->format('j F Y') }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Discharge Information -->
    @if($record->is_discharged)
    <div class="section">
        <div class="section-header">Discharge Information</div>
        <div class="grid">
            <div class="field">
                <div class="field-label">Discharge Status</div>
                <div class="field-value">
                    Discharged
                    <span class="badge badge-danger">Discharged</span>
                </div>
            </div>
            <div class="field">
                <div class="field-label">Mode of Discharge</div>
                <div class="field-value">{{ $record->mode_of_discharge ?? 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Date of Discharge</div>
                <div class="field-value">{{ $record->date_of_discharge ? \Carbon\Carbon::parse($record->date_of_discharge)->format('j F Y') : 'N/A' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Discharged By</div>
                <div class="field-value">{{ $record->dischargedBy->name ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Status Summary -->
    <div class="section">
        <div class="section-header">Current Status</div>
        <div class="grid grid-2">
            <div class="field">
                <div class="field-label">Discharge Status</div>
                <div class="field-value">
                    {{ $record->is_discharged ? 'Discharged' : 'In Custody' }}
                    @if(!$record->is_discharged)
                        <span class="badge">Active</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>
</html>