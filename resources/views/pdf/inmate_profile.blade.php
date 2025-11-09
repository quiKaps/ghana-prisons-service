<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Prisoner Profile - Print</title>
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

    .table-container {
        width: 100%;
        overflow-x: auto;
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    table thead {
        background: #000;
        color: #fff;
    }

    table th {
        padding: 12px 10px;
        text-align: left;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    table td {
        padding: 12px 10px;
        border-bottom: 1px solid #e0e0e0;
    }

    table tbody tr:hover {
        background: #f9f9f9;
    }

    .conviction-item {
        padding: 15px;
        border: 1px solid #ddd;
        margin-bottom: 15px;
        background: #fafafa;
    }

    .conviction-item .grid {
        gap: 15px;
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
<div class="station-name">{{ Auth::user()->station->name }}</div>
</div>
</div>
<!-- Personal Record Section -->
<div class="section">
    <div class="section-header">Personal Record</div>
    <div class="grid">
        <div class="field photo-section">
            <div class="field-label">Prisoner Photo</div>
            <div class="photo-container">
    @if($record->prisoner_picture && Storage::disk('public')->exists($record->prisoner_picture))
        <img src="{{ asset('storage/' . $record->prisoner_picture) }}" 
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
    <div class="field-label">Age on Admission</div>
    <div class="field-value">{{ $record->age_on_admission }} years</div>
</div>
<div class="field">
    <div class="field-label">Offence</div>
    <div class="field-value">{{ $record->latestSentenceByDate->offence }}</div>
</div>
<div class="field">
    <div class="field-label">Sentence</div>
    <div class="field-value">{{ $record->latestSentenceByDate->total_sentence }}</div>
</div>
<div class="field">
    <div class="field-label">Date of Admission</div>
    <div class="field-value">{{ $record->admission_date }}</div>
</div>
<div class="field">
    <div class="field-label">Date of Sentence</div>
    <div class="field-value">{{ $record->latestSentenceByDate->date_of_sentence }}</div>
</div>
<div class="field">
    <div class="field-label">EPD (Earliest Possible Date)</div>
    <div class="field-value">{{ $record->latestSentenceByDate->EPD }}</div>
</div>
<div class="field">
    <div class="field-label">LPD (Latest Possible Date)</div>
    <div class="field-value">{{ $record->latestSentenceByDate->LPD }}</div>
</div>
<div class="field">
    <div class="field-label">Court of Committal</div>
    <div class="field-value">{{ $record->latestSentenceByDate->court_of_committal }}</div>
</div>
<div class="field">
    <div class="field-label">Block & Cell</div>
    <div class="field-value">{{ $record->cell_id }}</div>
</div>
</div>
</div>

<!-- Transfer-In Information -->
<div class="section">
    <div class="section-header">Transfer-In Information</div>
    <div class="grid">
        <div class="field">
            <div class="field-label">Transferred Inmate</div>
            <div class="field-value">
                {{ $record->transferred_in ? 'Yes' : 'No' }}
@if($record->transferred_in)
<span class="badge">Transferred</span>
@endif
</div>
</div>
                    @if ($record->transferred_in)
<div class="field">
    <div class="field-label">Station Transferred From</div>
    <div class="field-value">{{ !empty($record->station_transferred_from_id) ?
        \App\Models\Station::where('id', $record->station_transferred_from_id)->pluck('name')->first() : ''
        }}</div>
</div>
<div class="field">
    <div class="field-label">Transferred Date</div>
    <div class="field-value">{{ $record->date_transferred_in }}</div>
</div>
@endif
</div>
</div>

<!-- Disability Information -->
<div class="section">
    <div class="section-header">Disability Information</div>
    <div class="grid">
        <div class="field">
            <div class="field-label">Disability Status</div>
            <div class="field-value">
                {{ $record->disability ? 'Yes' : 'No' }}
@if($record->disability)
<span class="badge">Disabled</span>
@endif
</div>
</div>
@if ($record->disability)
<div class="field">
    <div class="field-label">disability_type</div>
    <div class="field-value">{{ implode(', ', $record->disability_type) }}</div>
</div>

@endif
</div>
</div>

<!-- Social Background Section -->
<div class="section">
    <div class="section-header">Social Background</div>
    <div class="grid">
        <div class="field">
            <div class="field-label">Tribe</div>
            <div class="field-value">{{ $record->tribe }}</div>
        </div>
        <div class="field">
            <div class="field-label">Language Spoken</div>
            <div class="field-value">{{ is_array($record->languages_spoken) ? implode(', ',
                $record->languages_spoken) : ($record->languages_spoken ?? 'No Languages') }}</div>
        </div>
        <div class="field">
            <div class="field-label">Hometown</div>
            <div class="field-value">{{ $record->hometown }}</div>
        </div>
        <div class="field">
            <div class="field-label">Country of Origin</div>
            <div class="field-value">{{ $record->nationality }}</div>
        </div>
        <div class="field">
            <div class="field-label">Marital Status</div>
            <div class="field-value">{{ $record->married_status }}</div>
        </div>
        <div class="field">
            <div class="field-label">Education Background</div>
            <div class="field-value">{{ $record->education_level }}</div>
        </div>
        <div class="field">
            <div class="field-label">Religious Background</div>
            <div class="field-value">{{ $record->religion }}</div>
        </div>
        <div class="field">
            <div class="field-label">Occupation</div>
            <div class="field-value">{{ $record->occupation }}</div>
        </div>
        <div class="field">
            <div class="field-label">Name of Next of Kin</div>
            <div class="field-value">{{ $record->next_of_kin_name }}</div>
        </div>
        <div class="field">
            <div class="field-label">Next of Kin Relationship</div>
            <div class="field-value">{{ $record->next_of_kin_relationship ?? 'Son' }}</div>
        </div>
        <div class="field">
            <div class="field-label">Contact of Next of Kin</div>
            <div class="field-value">{{ $record->next_of_kin_contact }}</div>
        </div>
    </div>
</div>

<!-- Distinctive Body Marks Section -->
<div class="section">
    <div class="section-header">Distinctive Body Marks</div>
    <div class="grid grid-2">
        <div class="field">
            <div class="field-label">Distinctive Marks</div>
            <div class="field-value">{{ is_array($record->distinctive_marks) ? implode(', ',
                $record->distinctive_marks) : ($record->distinctive_marks ?? '') }}</div>
        </div>
        <div class="field">
            <div class="field-label">Part of the Body</div>
            <div class="field-value">{{ $record->part_of_the_body }}</div>
        </div>
    </div>
</div>

<!-- Previous Conviction Section -->
<div class="section">
    <div class="section-header">Previous Conviction</div>
    @if (!empty($record->previous_convictions) && is_array($record->previous_convictions))
    @foreach ($record->previous_convictions as $previous_conviction)
<div class="conviction-item">
    <div class="grid">
        <div class="field">
            <div class="field-label">Previous Sentence</div>
            <div class="field-value">{{ $previous_conviction['previous_sentence'] ?? '' }}</div>
        </div>
        <div class="field">
            <div class="field-label">Previous Offence</div>
            <div class="field-value">{{ $previous_conviction['previous_offence'] ?? '' }}</div>
        </div>
        <div class="field">
            <div class="field-label">Station</div>
            <div class="field-value">{{ !empty($previous_conviction['previous_station_id']) ?
                \App\Models\Station::where('id',
                $previous_conviction['previous_station_id'])->pluck('name')->first() : '' }}</div>
        </div>
    </div>
</div>
@endforeach
@else
<div class="no-data">No previous conviction recorded.</div>
@endif
</div>

<!-- Police Information Section -->
<div class="section">
    <div class="section-header">Police Information</div>
    <div class="grid">
        <div class="field">
            <div class="field-label">Police Name</div>
            <div class="field-value">{{ $record->police_name }}</div>
        </div>
        <div class="field">
            <div class="field-label">Police Station</div>
            <div class="field-value">{{ $record->police_station }}</div>
        </div>
        <div class="field">
            <div class="field-label">Police Contact</div>
            <div class="field-value">{{ $record->police_contact }}</div>
        </div>
    </div>
</div>

<!-- Sentences Section -->
<div class="section">
    <div class="section-header">Sentences</div>
    <div class="table-container">
        <table>
                <thead>
                    <tr>
<th>Sentence</th>
<th>Offence</th>
<th>EPD</th>
<th>LPD</th>
<th>Court of Committal</th>
<th>Committed By</th>
<th>Committed Sentence</th>
                    </tr>
                </thead>
                <tbody>
@if($record->sentences && count($record->sentences) > 0)
@foreach($record->sentences as $sentence)
                    <tr>
<td>{{ $sentence->total_sentence ?? '-' }}</td>
<td>{{ $sentence->offence ?? '-' }}</td>
<td>{{ $sentence->EPD ?? '-' }}</td>
<td>{{ $sentence->LPD ?? '-' }}</td>
<td>{{ $sentence->court_of_committal ?? '-' }}</td>
<td>{{ $sentence->committed_by ?? '-' }}</td>
<td>{{ $sentence->committed_sentence ?? '-' }}</td>
                    </tr>
@endforeach
@else
<tr>
    <td colspan="7" style="text-align: center; font-style: italic; color: #999;">No sentences
        recorded</td>
</tr>
@endif
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>