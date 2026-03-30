 {{-- ================================================================ --}}
    {{-- SCRIPTS                                                           --}}
    {{-- ================================================================ --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.3.2/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    let currentRowDataForEmail = null;
    let currentRow             = null;
    let selectedRowsData       = [];

    $(document).ready(function () {

        $('.select2').select2({ placeholder: "Select a branch", allowClear: true, width: '69%' });

        // ── DataTable ────────────────────────────────────────────────────
        var table = $('#reportsTable').DataTable({
            paging:        false,
            searching:     true,
            ordering:      true,
            info:          true,
            scrollX:       true,
            scrollY:       "500px",
            scrollCollapse: false,
            fixedHeader:   { header: true, footer: true },
            autoWidth:     true,
            dom: '<"top"<"d-flex justify-content-between align-items-center"<"d-flex"f><"d-flex"B>>>rt<"bottom"ip><"clear">',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Get Request Note',
                    exportOptions: {
                        columns: ':visible:not(:first-child):not(:nth-child(2))',
                        format: {
                            body: function (data, row, column, node) {
                                if ([3, 4, 5].includes(column))
                                    return $(node).find('.truncate-text').attr('title') || $(node).text().trim();
                                if ([6, 7, 9, 10, 20].includes(column)) {
                                    const v = parseFloat($(node).text().replace(/,/g, ''));
                                    return isNaN(v) ? '0' : v.toLocaleString('en-US');
                                }
                                return $(node).text().trim();
                            }
                        }
                    },
                    customizeData: function (data) {
                        const intVal = i => typeof i === 'string' ? i.replace(/[^\d.-]/g, '') * 1 : (typeof i === 'number' ? i : 0);
                        let totals   = new Array(data.body[0].length).fill('');
                        let sum      = data.body.reduce((acc, row) => acc + intVal(row[7]), 0);
                        totals[7]    = sum.toLocaleString('en-US');
                        totals[0]    = 'Total RI Sum Insured:';
                        data.body.push(totals);
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Get Request Note',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible:not(:first-child):not(:nth-child(2))',
                        format: {
                            body: function (data, row, column, node) {
                                if ([3, 4, 5].includes(column))
                                    return $(node).find('.truncate-text').attr('title') || $(node).text().trim();
                                if ([6, 7, 9, 10, 20].includes(column)) {
                                    const v = parseFloat(data.replace(/,/g, ''));
                                    return isNaN(v) ? '0' : v.toLocaleString('en-US');
                                }
                                return $(node).text().trim();
                            }
                        }
                    },
                    customize: function (doc) {
                        const intVal = i => typeof i === 'string' ? i.replace(/[^\d.-]/g, '') * 1 : (typeof i === 'number' ? i : 0);
                        let totals   = new Array(doc.content[1].table.body[0].length).fill('');
                        let sum      = doc.content[1].table.body.slice(1).reduce((acc, row) => acc + intVal(row[7].text), 0);
                        totals[7]    = { text: sum.toLocaleString('en-US'), bold: true };
                        totals[0]    = 'Total RI Sum Insured:';
                        doc.content[1].table.body.push(totals);
                    }
                }
            ],
            footerCallback: function (row, data, start, end, display) {
                var api    = this.api();
                var intVal = i => typeof i === 'string' ? parseFloat(i.replace(/[^\d.-]/g, '')) || 0 : typeof i === 'number' ? i : 0;
                var total  = api.column(9, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                $(api.column(9).footer()).html(total.toLocaleString('en-US'));
            },
            initComplete: function () {
                this.api().draw();
                this.api().columns.adjust();
                $('.dataTables_filter input').attr('placeholder', 'Search...');
                $('.dataTables_filter').css({ 'margin-left': '5px', 'margin-right': '5px' });
                $('.dt-buttons').css('margin-left', '5px');
            },
            drawCallback: function () { this.api().columns.adjust(); }
        });

        $('a[title="Reset"]').on('click', function () { setTimeout(() => table.draw(), 100); });

        // ── Checkboxes ───────────────────────────────────────────────────
        $('#selectAllCheckbox').on('change', function () {
            const checked = $(this).prop('checked');
            $('#reportsTable tbody tr').each(function () {
                $(this).find('.row-checkbox').prop('checked', checked);
            });
            updateBulkBar();
        });

        $(document).on('change', '.row-checkbox', function () {
            updateBulkBar();
            const total   = $('#reportsTable tbody tr').length;
            const checked = $('#reportsTable tbody .row-checkbox:checked').length;
            $('#selectAllCheckbox')
                .prop('indeterminate', checked > 0 && checked < total)
                .prop('checked', total > 0 && checked === total);
        });

        function updateBulkBar () {
            const count = $('#reportsTable tbody .row-checkbox:checked').length;
            $('#selectedCount').text(count);
            $('#bulkSendEmailBtn').prop('disabled', count === 0);
        }

        // ── Collect checked rows ─────────────────────────────────────────
        function collectSelectedRows () {
            const rows = [];
            $('#reportsTable tbody tr').each(function () {
                if (!$(this).find('.row-checkbox').prop('checked')) return;
                const $r   = $(this);
                const $pBtn = $r.find('.preview-pdf-btn');
                rows.push({
                    reqNote:           $r.find('td[data-field="request_note"]').text().trim(),
                    docDate:           $r.find('td[data-field="doc_date"]').text().trim(),
                    dept:              $r.find('td[data-field="dept"]').text().trim(),
                    businessDesc:      $r.find('td[data-field="business_desc"] .truncate-text').attr('title') || $r.find('td[data-field="business_desc"]').text().trim(),
                    insured:           $r.find('td[data-field="insured"] .truncate-text').attr('title')       || $r.find('td[data-field="insured"]').text().trim(),
                    reinsParty:        $r.find('td[data-field="reins_party"] .truncate-text').attr('title')   || $r.find('td[data-field="reins_party"]').text().trim(),
                    totalSumIns:       $r.find('td[data-field="total_sum_ins"]').text().trim(),
                    riSumIns:          $r.find('td[data-field="ri_sum_ins"]').text().trim(),
                    share:             $r.find('td[data-field="share"]').text().trim(),
                    totalPremium:      $r.find('td[data-field="total_premium"]').text().trim(),
                    riPremium:         $r.find('td[data-field="ri_premium"]').text().trim(),
                    commDate:          $r.find('td[data-field="comm_date"]').text().trim(),
                    expiryDate:        $r.find('td[data-field="expiry_date"]').text().trim(),
                    cp:                $r.find('td[data-field="cp"]').text().trim(),
                    convTakaful:       $r.find('td[data-field="conv_takaful"]').text().trim(),
                    posted:            $r.find('td[data-field="posted"]').text().trim(),
                    userName:          $r.find('td[data-field="user_name"]').text().trim(),
                    acceptanceDate:    $r.find('td[data-field="acceptance_date"]').text().trim(),
                    warrantyPeriod:    $r.find('td[data-field="warranty_period"]').text().trim(),
                    commPercent: $r.find('td[data-field="commission_percent"]').text().trim(),
                    commissionAmount:  $r.find('td[data-field="commission_amount"]').text().trim(),
                    acceptanceNo:      $r.find('td[data-field="acceptance_no"]').text().trim(),
                    totalSi:  $pBtn.data('total-si'),
                    riSi:     $pBtn.data('ri-si'),
                    riPre:    $pBtn.data('ri-pre'),
                    totalPre: $pBtn.data('total-pre'),
                });
            });
            return rows;
        }

        // ── Bulk email modal ─────────────────────────────────────────────
        $('#bulkSendEmailBtn').on('click', function () {
            selectedRowsData = collectSelectedRows();
            $('#bulkSelectedCountDisplay').text(selectedRowsData.length);
            const $tbody = $('#bulkPreviewBody').empty();
            selectedRowsData.forEach((r, i) => {
                $tbody.append(`<tr><td>${i+1}</td><td><strong>${r.reqNote}</strong></td><td>${r.insured}</td><td>${r.dept}</td><td>${r.docDate}</td></tr>`);
            });
            $('#bulkModalStatus').text('');
            $('#confirmBulkSendBtn').prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i>Send All Emails');
            $('#bulkEmailModal').modal('show');
        });

        $('#confirmBulkSendBtn').on('click', async function () {
            const to      = $('#bulkTo').val().trim();
            const cc      = $('#bulkCc').val().trim();
            const subject = $('#bulkSubject').val().trim();
            const body    = $('#bulkBody').val().trim();

            if (!to)      { alert('Please enter a recipient email address.'); return; }
            if (!subject) { alert('Please enter a subject prefix.'); return; }
            if (!body)    { alert('Please enter an email body.'); return; }

            const $btn      = $(this);
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Generating PDFs…');
            $('#bulkModalStatus').html('<i class="bi bi-hourglass-split me-1"></i> Generating PDFs, please wait…');

            try {
                const pdfBase64Array = await Promise.all(
                    selectedRowsData.map(record =>
                        new Promise((resolve, reject) => {
                            try {
                                pdfMake.createPdf(createPdfDefinition(record)).getBase64(resolve);
                            } catch (e) { reject(e); }
                        })
                    )
                );

                const notesList   = selectedRowsData.map(r => r.reqNote).join(', ');
                const fullSubject = `${subject} [${notesList}]`;

                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Sending email…');
                $('#bulkModalStatus').html('<i class="bi bi-hourglass-split me-1"></i> Sending one email with all PDFs…');

                const recordsWithPdf = selectedRowsData.map((record, i) => ({
                    ...record,
                    pdfBase64:   pdfBase64Array[i],
                    pdfFilename: `Request_Note_${record.reqNote}.pdf`
                }));

                const response = await $.ajax({
                    url: "{{ route('send.email') }}",
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    traditional: false,
                    data: { _token: csrfToken, to, cc, subject: fullSubject, body, records: recordsWithPdf, pdfs: pdfBase64Array }
                });

                $btn.prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i>Send All Emails');
                $('#bulkModalStatus').text('');
                $('#bulkEmailModal').modal('hide');
                $('.row-checkbox, #selectAllCheckbox').prop('checked', false).prop('indeterminate', false);
                updateBulkBar();

                if (response.success) { alert('Email sent!'); location.reload(); }
                else                  { alert('❌ Error: ' + response.message); }

            } catch (err) {
                $btn.prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i>Send All Emails');
                $('#bulkModalStatus').text('');
                alert('❌ Failed: ' + (err.responseJSON?.message || err.message || 'Unknown error'));
            }
        });

        // ── PDF buttons ──────────────────────────────────────────────────
        $(document).on('click', '.preview-pdf-btn', function () {
            pdfMake.createPdf(createPdfDefinition($(this).data())).open();
        });

        $(document).on('click', '.download-pdf-btn', function () {
            const d = $(this).data();
            pdfMake.createPdf(createPdfDefinition(d)).download(`Request_Note_${d.reqNote || 'document'}.pdf`);
        });

        function createPdfDefinition (rowData) {
            return {
                content: [
                    { text: 'Atlas Insurance Ltd.', style: 'header', alignment: 'center', margin: [0, 5, 0, 5] },
                    { text: `${rowData.dept || 'Fire'} Reinsurance Request Note`, style: 'header', alignment: 'center', margin: [0, 0, 0, 10] },
                    {
                        table: { widths: ['*', '*', '*'], body: [[ '', '', {
                            table: { widths: ['auto', '*'], body: [
                                ['Date:',               rowData.docDate || '10/01/2025'],
                                ['Request Note#:',      rowData.reqNote || 'N/A'],
                                ['Base Request Note#:', '-']
                            ]}, layout: 'noBorders'
                        }]]}, layout: 'noBorders', margin: [0, 0, 0, 10]
                    },
                    {
                        table: { widths: ['*'], body: [[{ stack: [{
                            table: { widths: ['auto', '*'], body: [
                                ['CLASS OF BUSINESS',    rowData.businessDesc || 'N/A'],
                                ['DESCRIPTION',          'Co-Insurers Panel:\n   IGI............. 30%\n   Askari.......... 10%\n   Habib........... 10%\n   Atlas........... 20%\n   Shaheen ........ 10%\n   TPL............. 20%\n   -----------------------\n   Total........... 100%\n\nDeductible: 5% of loss amount minimum Rs. 250,000/- on each & every loss.\nPolicy issued by cancelling & replacing Cover Note # 2024ISB-IIFCMIIT00177'],
                                ['INSURED NAME',         rowData.insured || 'N/A'],
                                ['NTN Number',           '4505300-8'],
                                ['C NOTE/POLICY#',       '2025ISB-IIFCMIIP00002'],
                                ['SITUATION',            'Plot no.A-19 & A-20, M-3 Industrial City Road, M-3, Industrial City, Faisalabad.'],
                                ['CONSTRUCTION CLASS',   '1st Class'],
                                ['RI Reference',         'null'],
                                ['PERIOD OF INSURANCE',  rowData.commDate],
                                ['PERIOD OF RE-INSURANCE', rowData.expiryDate],
                                ['SUM INSURED (Our Share)', rowData.totalSi ? rowData.totalSi.toLocaleString() : 'N/A'],
                                ['ATLAS SHARE',          rowData.share ? parseFloat(rowData.share).toFixed(2) + '%' : 'N/A'],
                                ['GROSS PREMIUM RATE',   rowData.grossPremiumRate ? parseFloat(rowData.grossPremiumRate).toFixed(4) + '%' : '0.0872%'],
                                ['RI COMMISSION',        rowData.commPercent ? parseFloat(rowData.commPercent).toFixed(2) + '%' : '25.00%']
                            ]}, layout: 'noBorders', margin: [0, 0, 0, 15]
                        }]}]]},
                        layout: { hLineWidth: () => 1, vLineWidth: () => 1, hLineColor: () => 'black', vLineColor: () => 'black', paddingLeft: () => 6, paddingRight: () => 6, paddingTop: () => 6, paddingBottom: () => 6 }
                    },
                    {
                        text: [
                            'DETAILS:\n',
                            '1. Closing Particular submission (60) days.\n',
                            '2. Simultaneous Payment Clause\n',
                            '3. The sixty (60) days period for the Closing Particulars shall commence on the first day of the Risk Period.\n',
                            '4. 20% Automatic Escalation in Sum Insured\n',
                            '5. Automatic Period extension clause\n',
                            'All other details, terms, conditions, warranties, subjectivities and exclusions as per original policy.'
                        ], margin: [0, 10, 0, 10]
                    },
                    {
                        table: { widths: ['*', 50, 110, '*'], body: [
                            [
                                { text: 'REINSURER',               style: 'tableHeader', border: [true,true,true,true] },
                                { text: 'FAC RI OFFERED',          style: 'tableHeader', colSpan: 2, alignment: 'center', border: [true,true,true,true] },
                                {},
                                { text: 'SIGNATURE OF ACCEPTANCE', style: 'tableHeader', border: [true,true,true,true] }
                            ],
                            [
                                { text: '',       border: [true,true,true,false] },
                                { text: '%',      style: 'tableHeader', border: [true,true,true,true] },
                                { text: 'AMOUNT', alignment: 'right', style: 'tableHeader', border: [true,true,true,true] },
                                { text: '',       border: [true,true,true,false] }
                            ],
                            [
                                { text: rowData.reinsParty,           alignment: 'center', border: [true,false,true,true] },
                                { text: rowData.share || '14.70%',    alignment: 'center', border: [true,false,true,true] },
                                { text: rowData.riSi  || '239,478,694', alignment: 'right', border: [true,false,true,true] },
                                { text: '',                            border: [true,false,true,true] }
                            ]
                        ]},
                        layout: {
                            hLineWidth: (i, node) => (i === 0 || i === 1 || i === 2 || i === node.table.body.length) ? 1 : 0,
                            vLineWidth: () => 1, hLineColor: () => 'black', vLineColor: () => 'black',
                            paddingLeft: () => 5, paddingRight: () => 5, paddingTop: () => 5, paddingBottom: () => 5
                        }, margin: [0, 0, 0, 10]
                    },
                    { text: 'For And On Behalf Of\nATLAS INSURANCE LTD.', alignment: 'right', margin: [0, 0, 0, 5] },
                    { text: 'Page 1 of 1', alignment: 'center', margin: [0, 5, 0, 0] }
                ],
                styles: {
                    header:      { fontSize: 12, bold: true },
                    tableHeader: { bold: true, fontSize: 10, fillColor: '#f0f0f0', alignment: 'center' }
                },
                defaultStyle: { fontSize: 10 }
            };
        }

        // ── Note Tag Modal ───────────────────────────────────────────────
        $(document).on('click', '.note-tag-btn', function () {
            const $btn = $(this);
            $('#ntHiddenRef').val($btn.data('req-note'));
            $('#ntRefNo').text($btn.data('req-note'));
            $('#ntAction').val('').removeClass('is-invalid');
            $('#ntRemarks').val('');
            $('#ntCharCount').text('0');
            $('#ntSubmitError').addClass('d-none');
            $('#ntSubmitErrorMsg').text('');
            $('#ntSubmitBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Note');
            $('#noteTagModal').modal('show');
        });

        $('#ntRemarks').on('input', function () {
            $('#ntCharCount').text($(this).val().length);
        });

        $('#ntAction').on('change', function () {
            $(this).removeClass('is-invalid');
            $('#ntSubmitError').addClass('d-none');
        });

        $('#ntSubmitBtn').on('click', function () {
            const refNo   = $('#ntHiddenRef').val();
            const action  = $('#ntAction').val();
            const remarks = $('#ntRemarks').val().trim();

            if (!action) {
                $('#ntAction').addClass('is-invalid');
                $('#ntSubmitError').removeClass('d-none');
                $('#ntSubmitErrorMsg').text('Please select an action (Revise or Cancel).');
                return;
            }

            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving…');
            $('#ntSubmitError').addClass('d-none');

            $.ajax({
                url:     "{{ route('reins.tag.store') }}",
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data:    { GRH_REFERENCE_NO: refNo, tag_action: action, remarks: remarks },
                success: function (response) {
                    if (response.success) {
                        $('#noteTagModal').modal('hide');
                        location.reload();
                    } else {
                        $('#ntSubmitError').removeClass('d-none');
                        $('#ntSubmitErrorMsg').text(response.message || 'Failed to save note.');
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Note');
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.errors
                        ? Object.values(xhr.responseJSON.errors).flat().join(' ')
                        : (xhr.responseJSON?.message || 'Server error. Please try again.');
                    $('#ntSubmitError').removeClass('d-none');
                    $('#ntSubmitErrorMsg').text(msg);
                    $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Note');
                }
            });
        });

        // ── Aging card modal ─────────────────────────────────────────────
        const filteredRecordsModal = new bootstrap.Modal(
            document.getElementById('filteredRecordsModal'),
            { backdrop: 'static', keyboard: false }
        );
        let allFilteredRows = [];

        function calculateDaysDiff (docDate) {
            try {
                if (!docDate || docDate === 'N/A' || docDate === 'Invalid Date') return -1;
                const [day, month, year] = docDate.split('-').map(Number);
                const parsed = new Date(year, month - 1, day);
                if (isNaN(parsed.getTime())) return -1;
                const diff = Math.floor((new Date() - parsed) / 86400000);
                return diff >= 0 ? diff : -1;
            } catch (e) { return -1; }
        }

        $('.aging-card').on('click', function () {
            const minDays = parseInt($(this).data('min-days'));
            const maxDays = parseInt($(this).data('max-days'));
            const label   = $(this).data('label');

            $('#filteredRecordsBody').empty();
            allFilteredRows = [];
            let riTotal = 0;

            $('#reportsTable tbody tr').each(function () {
                const $row   = $(this);
                const docDate = $row.find('td[data-field="doc_date"]').text().trim();
                const riText  = $row.find('td[data-field="ri_sum_ins"]').text().trim();
                const ri      = riText === 'N/A' ? 0 : (parseFloat(riText.replace(/,/g, '')) || 0);
                const days    = calculateDaysDiff(docDate);

                if (days >= minDays && days <= maxDays) {
                    const reqNote     = $row.find('td[data-field="request_note"]').text().trim();
                    const dept        = $row.find('td[data-field="dept"]').text().trim();
                    const bizDescFull = $row.find('td[data-field="business_desc"] .truncate-text').attr('title') || $row.find('td[data-field="business_desc"]').text().trim();
                    const insuredFull = $row.find('td[data-field="insured"] .truncate-text').attr('title')       || $row.find('td[data-field="insured"]').text().trim();
                    const reinsFull   = $row.find('td[data-field="reins_party"] .truncate-text').attr('title')   || $row.find('td[data-field="reins_party"]').text().trim();
                    const totSi       = $row.find('td[data-field="total_sum_ins"]').text().trim();
                    const riSi        = $row.find('td[data-field="ri_sum_ins"]').text().trim();
                    const share       = $row.find('td[data-field="share"]').text().trim();
                    const totPre      = $row.find('td[data-field="total_premium"]').text().trim();
                    const riPre       = $row.find('td[data-field="ri_premium"]').text().trim();
                    const commDate    = $row.find('td[data-field="comm_date"]').text().trim();
                    const expiryDate  = $row.find('td[data-field="expiry_date"]').text().trim();
                    const cp          = $row.find('td[data-field="cp"]').text().trim();
                    const convTak     = $row.find('td[data-field="conv_takaful"]').text().trim();
                    const posted      = $row.find('td[data-field="posted"]').text().trim();
                    const userName    = $row.find('td[data-field="user_name"]').text().trim();
                    const accDate     = $row.find('td[data-field="acceptance_date"]').text().trim();
                    const warPeriod   = $row.find('td[data-field="warranty_period"]').text().trim();
                    const commPercent     = $row.find('td[data-field="commission_percent"]').text().trim();
                    const commAmt     = $row.find('td[data-field="commission_amount"]').text().trim();
                    const accNo       = $row.find('td[data-field="acceptance_no"]').text().trim();

                    const $newRow = $(`<tr>
                        <td>${reqNote}</td><td>${docDate}</td><td>${dept}</td>
                        <td><span title="${bizDescFull}">${bizDescFull}</span></td>
                        <td><span title="${insuredFull}">${insuredFull}</span></td>
                        <td><span title="${reinsFull}">${reinsFull}</span></td>
                        <td class="text-end">${totSi}</td><td class="text-end">${riSi}</td>
                        <td class="text-end">${share}</td><td class="text-end">${totPre}</td>
                        <td class="text-end">${riPre}</td><td>${commDate}</td><td>${expiryDate}</td>
                        <td>${cp}</td><td>${convTak}</td><td>${posted}</td><td>${userName}</td>
                        <td>${accDate}</td><td>${warPeriod}</td>
                        <td class="text-end">${commPercent}</td><td class="text-end">${commAmt}</td>
                        <td>${accNo}</td>
                    </tr>`);

                    $('#filteredRecordsBody').append($newRow);
                    allFilteredRows.push({ element: $newRow, riSumIns: ri });
                    riTotal += ri;
                }
            });

            $('#filteredRecordsTable tfoot').remove();
            $('#filteredRecordsTable').append(`
                <tfoot>
                    <tr class="table-active">
                        <td colspan="7" class="text-end fw-bold">Total RI Sum Insured:</td>
                        <td class="fw-bold">${riTotal.toLocaleString('en-US')}</td>
                        <td colspan="14"></td>
                    </tr>
                </tfoot>`);

            $('#filteredRecordsTitle').text(`${label} — ${allFilteredRows.length} records`);
            $('#modalRecordCount').text(`Showing ${allFilteredRows.length} records`);
            filteredRecordsModal.show();
        });

        $('#modalSearch').on('keyup', function () {
            const val = $(this).val().toLowerCase();
            let visible = 0, riTotal = 0;
            allFilteredRows.forEach(r => {
                const show = r.element.text().toLowerCase().includes(val);
                r.element.toggle(show);
                if (show) { visible++; riTotal += r.riSumIns; }
            });
            $('#filteredRecordsTable tfoot td.fw-bold').last().text(riTotal.toLocaleString('en-US'));
            $('#modalRecordCount').text(`Showing ${visible} filtered records`);
        });

        $('#clearModalSearch').on('click', function () {
            $('#modalSearch').val('');
            let riTotal = 0;
            allFilteredRows.forEach(r => { r.element.show(); riTotal += r.riSumIns; });
            $('#filteredRecordsTable tfoot td.fw-bold').last().text(riTotal.toLocaleString('en-US'));
            $('#modalRecordCount').text(`Showing ${allFilteredRows.length} records`);
        });

        // ── Request Note click → detailsModal ───────────────────────────
        $(document).on('click', '.open-modal', function (e) {
            e.preventDefault();
            const reqNote = $(this).data('req-note');
            $('#modalBody').html('<p class="text-center text-muted py-4">Loading...</p>');
            $('#detailsModal').modal('show');

            $.ajax({
                url:      'http://192.168.170.24/dashboardApi/reins/rqn/get_notes_dtl.php',
                method:   'GET',
                data:     { req_note: reqNote },
                dataType: 'json',
                success: function (data) {
                    if (!data || data.length === 0) {
                        $('#modalBody').html('<p class="text-center text-muted">No records found.</p>');
                        return;
                    }
                    let html = '<div class="table-responsive"><table class="table table-bordered table-striped table-sm"><thead class="table-light"><tr>';
                    const headers = ['Doc No','Insured','Reinsurer','Share','Commission','Accepted Date','Comm. Date','Expiry Date','Issue Date','Ceded SI','Ceded Premium','SI','Premium','CP No','CP Date','Ref No'];
                    headers.forEach(h => html += `<th>${h}</th>`);
                    html += '</tr></thead><tbody>';
                    data.forEach(function (item) {
                        const r = typeof item === 'string' ? JSON.parse(item) : item;
                        html += `<tr>
                            <td>${r.GRD_CEDINGDOCNO || ''}</td>
                            <td>${r.INSURED_DESC || ''}</td>
                            <td>${r.RE_COMP_DESC || ''}</td>
                            <td>${r.GRH_CEDEDSISHARE || ''}%</td>
                            <td>${r.GRH_COMMISSIONRATE || ''}%</td>
                            <td>${r.GRH_ACCEPTEDDATE || ''}</td>
                            <td>${r.GDH_COMMDATE || ''}</td>
                            <td>${r.GDH_EXPIRYDATE || ''}</td>
                            <td>${r.GDH_ISSUEDATE || ''}</td>
                            <td class="text-end">${formatNumber(r['SUM(GRS_CEDEDSI)'])}</td>
                            <td class="text-end">${formatNumber(r['SUM(GRS_CEDEDPREM)'])}</td>
                            <td class="text-end">${formatNumber(r['SUM(GRS_TOTALSI)'])}</td>
                            <td class="text-end">${formatNumber(r['SUM(GRS_TOTALPREM)'])}</td>
                            <td>${r.GCP_DOC_REFERENCENO || ''}</td>
                            <td>${r.CREATE_DATE || ''}</td>
                            <td>${r.GCT_THEIR_REF_NO || ''}</td>
                        </tr>`;
                    });
                    html += '</tbody></table></div>';
                    $('#modalBody').html(html);
                },
                error: function () {
                    $('#modalBody').html('<p class="text-center text-danger">Error loading data. Please try again.</p>');
                }
            });
        });

    }); // end document.ready

    function formatNumber(num) {
        if (!num) return '0';
        return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    </script>