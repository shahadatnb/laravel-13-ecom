class MoneyReceiptGenerator {
    constructor(doc, options = {}) {
        this.doc = doc;
        this.options = {
            instituteName: "ASIAN CODER",
            instituteAddress: "Rajshahi, Bangladesh",
            borderColor: [0, 0, 0],
            borderWidth: .05,
            orientation: 'portrait',
            ...options
        };
    }

    generateReceipt(paymentData, receiptType = 'both') {
        const doc = this.doc;
        const pageWidth = doc.internal.pageSize.width;
        const pageHeight = doc.internal.pageSize.height;
        
        doc.setProperties({
            title: `Money Receipt - ${paymentData.invoice.invoice_id}`,
            subject: 'Payment Receipt',
            author: this.options.instituteName
        });

        // বর্ডার অ্যাড করুন
        //this.addBorder();

        // পেজের মাঝে ভার্টিক্যাল লাইন
        doc.setLineDashPattern([1, 2], 0); // [1mm ডট, 2mm গ্যাপ]
        doc.setDrawColor(0, 0, 0);
        doc.setLineWidth(0.5);
        doc.line(pageWidth / 2, 5, pageWidth / 2, pageHeight / 2 - 10);
        doc.setLineDashPattern([], 0);

        if (receiptType === 'both' || receiptType === 'institute') {
            // বাম পাশে - Institute Copy
            this.addInstituteCopy(paymentData, 10, 10, pageWidth / 2 - 10);
        }

        if (receiptType === 'both' || receiptType === 'student') {
            // ডান পাশে - Student Copy
            this.addStudentCopy(paymentData, pageWidth / 2 + 5, 10, pageWidth / 2 - 10);
        }

        return doc;
    }

    addBorder() {
        const doc = this.doc;
        const pageWidth = doc.internal.pageSize.width;
        const pageHeight = doc.internal.pageSize.height;
        
        doc.setDrawColor(...this.options.borderColor);
        doc.setLineWidth(this.options.borderWidth);
        doc.rect(5, 5, pageWidth - 10, pageHeight - 10);
    }

    addInstituteCopy(paymentData, startX, startY, maxWidth) {
        const doc = this.doc;
        let yPosition = startY;

        // হেডার - Institute Copy
        doc.setFontSize(8);
        doc.setFont('helvetica', 'bold');
        doc.text("Institute Copy", startX, yPosition, { align: 'left' });
        yPosition += 3;
        yPosition = this.addHeader(doc, paymentData, startX, yPosition, maxWidth);

        // স্টুডেন্ট ইনফরমেশন
        yPosition = this.addStudentInfo(doc, paymentData, startX, yPosition, maxWidth);
        yPosition += 2;

        // ফি টেবিল
        yPosition = this.addFeeTable(doc, paymentData, startX, yPosition, maxWidth);
        yPosition += 2;

        // টোটাল সামারি
        //yPosition = this.addTotalSummary(doc, paymentData, startX, yPosition, maxWidth);
        //yPosition += 15;

        // পেমেন্ট ইন ওয়ার্ড
        //this.addPaymentInWords(doc, paymentData, startX, yPosition, maxWidth);
        //yPosition += 3;

        // ফুটার ইনফো
        this.addFooterInfo(doc, paymentData, startX, yPosition, maxWidth);
    }

    addStudentCopy(paymentData, startX, startY, maxWidth) {
        const doc = this.doc;
        let yPosition = startY;

        // হেডার - Student Copy
        doc.setFontSize(8);
        doc.setFont('helvetica', 'bold');
        doc.text("Student Copy", startX, yPosition, { align: 'left' });
        yPosition += 3;
        yPosition = this.addHeader(doc, paymentData, startX, yPosition, maxWidth);

        // স্টুডেন্ট ইনফরমেশন
        yPosition = this.addStudentInfo(doc, paymentData, startX, yPosition, maxWidth);
        yPosition += 2;

        // ফি টেবিল
        yPosition = this.addFeeTable(doc, paymentData, startX, yPosition, maxWidth);
        yPosition += 2;

        // টোটাল সামারি
        //yPosition = this.addTotalSummary(doc, paymentData, startX, yPosition, maxWidth);
        //yPosition += 15;

        // পেমেন্ট ইন ওয়ার্ড
        //this.addPaymentInWords(doc, paymentData, startX, yPosition, maxWidth);
        //yPosition += 3;

        // ফুটার ইনফো
        this.addFooterInfo(doc, paymentData, startX, yPosition, maxWidth);
    }

    addHeader(doc, paymentData, startX, yPosition, maxWidth) {
        let currentY = yPosition;
        // ইনস্টিটিউট নাম
        doc.setFontSize(10);
        doc.text(this.options.instituteName, startX + maxWidth/2, currentY, { align: 'center' });
        currentY += 3;

        // ঠিকানা
        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');
        doc.text(this.options.instituteAddress, startX + maxWidth/2, currentY, { align: 'center' });
        currentY += 5;

        // টাইটেল
        doc.setFontSize(10);
        doc.setFont('helvetica', 'bold');
        doc.text("Money Receipt", startX + maxWidth/2, currentY, { align: 'center' });
        currentY += 3;



        return currentY;
    }

    addStudentInfo(doc, paymentData, startX, yPosition, maxWidth) {        
        let currentY = yPosition;

        let tableData = [
            ['Student ID',':', paymentData.invoice.student_info.student.reg_no],
            ['Name',':', paymentData.invoice.student_info.student.name],
            ['Section',':', paymentData.invoice.student_info.section.name],
            ['Roll No',':', paymentData.invoice.student_info.class_roll],
            //['Class',':', paymentData.invoice.class],
            //['Session',':', paymentData.invoice.session]
        ];

        doc.autoTable({ 
            body: tableData,
            margin: { left: startX, right: startX },
            startY: currentY,
            tableWidth: maxWidth - 5,
            theme: 'plain',
            styles: {
              fontSize: 8,
              lineWidth:0,
              textColor: [0, 0, 0],
              halign: 'left',
              valign: 'middle',
              cellPadding: .25
            }
          });

        return doc.lastAutoTable.finalY;
    }

    addFeeTable(doc, paymentData, startX, yPosition, maxWidth) {
        let currentY = yPosition;
        
        let item = paymentData.invoice_items.map(item => {
            return [
                item.sub_head.head.name,
                item.sub_head.name,
                item.amount,
                item.waiver_amount,
                item.fine_amount,
                item.payable_amount
            ];
        });

        const amountInWords = this.numberToWords(paymentData.invoice.payable_amount) + ' Taka Only';
        let foot = [
            [{content:'Note:'+ (paymentData.invoice.note != 'null' ? paymentData.invoice.note : ''),colSpan: 3},{content:'Total Payable',colSpan: 2},{content:paymentData.invoice.payable_amount, styles: { halign: 'right', fontStyle: 'bold' } }],
            [{content:'In Words:'+ amountInWords, colSpan: 6}]
        ];

        doc.autoTable({
            head: [['Fee Head', 'Fee Sub Head', 'Amount', 'Waiver', 'Fine', 'Total']],
            body: item,
            foot: foot,
            margin: { left: startX, right: startX },
            startY: currentY,
            tableWidth: maxWidth - 5,
            theme: 'grid',
            columnStyles: {
                0: { halign: 'left' },
                1: { halign: 'left' },
                2: { halign: 'right' },
                3: { halign: 'right' },
                4: { halign: 'right' },
                5: { halign: 'right' }
            },
            headStyles: {
                fillColor: [221, 221, 221],
            },
            footStyles: {
                fillColor: false,
                fontStyle: 'normal'
            },
            styles: {
              fontSize: 8,
              lineWidth:.25,
              lineColor: [0, 0, 0],
              textColor: [0, 0, 0],
              halign: 'left',
              valign: 'top',
              cellPadding: 1
            }
        });        
        
        currentY = doc.lastAutoTable.finalY;

        /*
        // Note
        doc.setFont('helvetica', 'bold');
        doc.text("Note:", startX, currentY);
        
        if (paymentData.note) {
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(8);
            const noteLines = doc.splitTextToSize(paymentData.note, maxWidth);
            doc.text(noteLines, startX + 10, currentY);
            currentY += noteLines.length * 3 + 5;
        } else {
            currentY += 5;
        }
        */
        return currentY;
    }


    addPaymentInWords(doc, paymentData, startX, yPosition, maxWidth) {
        // বিভাজক লাইন
        doc.setDrawColor(0, 0, 0);
        doc.setLineWidth(0.5);
        doc.line(startX, yPosition - 5, startX + maxWidth - 5, yPosition - 5);
        
        // টাইটেল
        doc.setFontSize(10);
        doc.setFont('helvetica', 'bold');
        doc.text("In Word:", startX, yPosition);
        
        // Amount in words
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        const amountInWords = this.numberToWords(paymentData.invoice.payable_amount);
        doc.text(amountInWords + " Taka Only", startX + 15, yPosition);
        
        return yPosition + 5;
    }

    addFooterInfo(doc, paymentData, startX, yPosition, maxWidth) {
        doc.setFontSize(9);
        doc.setFont('helvetica', 'normal');
        
        let currentY = yPosition;

        let tableData = [
            ['Academic Year',':', paymentData.invoice.academic_year.year],
            ['Invoice ID',':', paymentData.invoice.invoice_id],
            ['Payment Date',':', paymentData.invoice.payment_date],
            ['Collected By',':', paymentData.invoice.created_by.name]
        ];

        doc.autoTable({ 
            body: tableData,
            margin: { left: startX, right: startX },
            startY: currentY,
            tableWidth: maxWidth - 45,
            theme: 'plain',
            styles: {
              fontSize: 8,
              lineWidth:0,
              textColor: [0, 0, 0],
              halign: 'left',
              valign: 'middle',
              cellPadding: .25
            }
          });

        //currentY = doc.lastAutoTable.finalY;
        
        /*
        // Academic Year
        doc.text("Academic Year : ", startX, currentY);
        doc.setFont('helvetica', 'bold');
        doc.text(paymentData.academicYear, startX + 35, currentY);
        
        // Invoice ID
        doc.setFont('helvetica', 'normal');
        currentY += 5;
        doc.text("Invoice ID : ", startX, currentY);
        doc.setFont('helvetica', 'bold');
        doc.text(paymentData.invoiceId, startX + 30, currentY);
        
        // Payment Date
        doc.setFont('helvetica', 'normal');
        currentY += 5;
        doc.text("Payment Date : ", startX, currentY);
        doc.setFont('helvetica', 'bold');
        doc.text(paymentData.paymentDate, startX + 35, currentY);
        
        // Collected By
        currentY += 5;
        doc.setFont('helvetica', 'normal');
        doc.text("Collected By : ", startX, currentY);
        doc.setFont('helvetica', 'bold');
        doc.text(paymentData.collectedBy, startX + 35, currentY);
        */

        // সাইনচার লাইন
        doc.setDrawColor(0, 0, 0);
        doc.setLineWidth(0.5);
        currentY += 5;
        doc.line(startX + maxWidth/2 +5, currentY + 5, startX + maxWidth/2 + 40, currentY + 5);
        
        // Signature
        currentY += 9;
        doc.setFont('helvetica', 'normal');
        doc.text("Accountant Signature", startX + maxWidth/2 + 23, currentY, { align: 'center' });
        
    }

    numberToWords(amount) {
        // সংখ্যাকে কথায় রূপান্তর করার সহজ ফাংশন
        const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        
        let num = Math.floor(amount);
        
        if (num === 0) return 'Zero';
        
        let words = '';
        
        // Thousands
        if (num >= 1000) {
            words += this.numberToWords(Math.floor(num / 1000)) + ' Thousand ';
            num %= 1000;
        }
        
        // Hundreds
        if (num >= 100) {
            words += ones[Math.floor(num / 100)] + ' Hundred ';
            num %= 100;
        }
        
        // Tens and Ones
        if (num >= 10 && num < 20) {
            words += teens[num - 10] + ' ';
        } else {
            if (num >= 20) {
                words += tens[Math.floor(num / 10)] + ' ';
                num %= 10;
            }
            if (num > 0) {
                words += ones[num] + ' ';
            }
        }
        
        return words.trim();
    }
}

// ব্যবহারের ফাংশন
function generateMoneyReceipt(paymentData, receiptType = 'both') {
    const doc = new jspdf.jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: 'a4'
    });
    
    const receipt = new MoneyReceiptGenerator(doc, {
        instituteName: "Masud UL Haque Institute",
        instituteAddress: "Chapainawabganj Sadar"
    });

    receipt.generateReceipt(paymentData, receiptType);
    
    return doc;
}

// ডেমো ডাটা
$("#generate_receipt").click(function() {
    const paymentData = {
        studentId: "25100003",
        studentName: "Mst. Sadia Khatun",
        section: "Six-Day-A",
        rollNo: 3,
        mobileNo: "00000000002",
        feeItems: [
            {
                feeHead: "Admission Fee",
                feeSubHeads: "Admission Fee",
                waiver: 0.0,
                fine: 50.0,
                payable: 550.0
            },
            {
                feeHead: "Tuition Fee",
                feeSubHeads: "January, February, March, April, May, June, July, August, September, October, November, December",
                waiver: 0.0,
                fine: 110.04,
                payable: 1310.0
            }
        ],
        note: "",
        totalPayable: 1860.00,
        paidAmount: 1860.00,
        dueAmount: 0.0,
        academicYear: "2025",
        invoiceId: "25301380000003",
        paymentDate: "09-12-2025",
        collectedBy: "Naslul Alam"
    };

    const doc = generateMoneyReceipt(paymentData, 'both');
    doc.save(`Money_Receipt_${paymentData.studentName}.pdf`);
});

// শুধুমাত্র Institute Copy
$("#generate_institute_copy").click(function() {
    const paymentData = { /* same as above */ };
    const doc = generateMoneyReceipt(paymentData, 'institute');
    doc.save('Institute_Copy.pdf');
});

// শুধুমাত্র Student Copy
$("#generate_student_copy").click(function() {
    const paymentData = { /* same as above */ };
    const doc = generateMoneyReceipt(paymentData, 'student');
    doc.save('Student_Copy.pdf');
});