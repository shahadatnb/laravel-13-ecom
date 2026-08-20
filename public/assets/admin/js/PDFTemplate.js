class PDFTemplate {
    constructor(doc, options = {}) {
        this.doc = doc;
        this.options = {
            companyName: "Asian Coder",
            companyAddress: "Rajshahi, Bangladesh",
            logo: null,
            ...options
        };
    }
    
    addHeader(title, subtitle = null) {
        const pageWidth = this.doc.internal.pageSize.width;
        
        // হেডার ব্যাকগ্রাউন্ড
        //this.doc.setFillColor(44, 62, 80);
        //this.doc.rect(0, 0, pageWidth, 40, 'F');
        
        // কোম্পানি নাম
        this.doc.setFontSize(15);
        this.doc.setFont('helvetica', 'bold');
        //this.doc.setTextColor(255, 255, 255);
        this.doc.text(this.options.companyName, pageWidth / 2, 12, {align: 'center'});
        
        // কোম্পানি ঠিকানা
        this.doc.setFontSize(9);
        this.doc.text(this.options.companyAddress,  pageWidth / 2, 16, {align: 'center'});
        
        // টাইটেল
        this.doc.setFontSize(11);
        this.doc.text(title, pageWidth / 2, 22, { align: 'center' });
        
        // সাবটাইটেল
        if (subtitle) {
            this.doc.setFontSize(10);
            this.doc.setFont('helvetica', 'normal');
            this.doc.text(subtitle, pageWidth / 2, 23, { align: 'center' });
            return 28; // startY
        }else{
            return 25; // startY
        }
        
    }
    
    addFooter() {
        const pageWidth = this.doc.internal.pageSize.width;
        const pageHeight = this.doc.internal.pageSize.height;
        const pageCount = this.doc.internal.getNumberOfPages();
        
        for (let i = 1; i <= pageCount; i++) {
            this.doc.setPage(i);
            
            this.doc.setFontSize(8);
            this.doc.setTextColor(100, 100, 100);
            
            // বটম লাইন
            this.doc.setDrawColor(200, 200, 200);
            this.doc.line(7, pageHeight - 10, pageWidth - 7, pageHeight - 10);
            
            //this.doc.text(`© 2024 ${this.options.companyName}. All rights reserved.`, pageWidth / 2, pageHeight - 15, { align: 'center' });
            this.doc.text(`Page ${i} of ${pageCount}`, pageWidth / 2, pageHeight - 7, { align: 'center' });                         
                    
            //মেটা ইনফো
            this.doc.setFontSize(7);
            this.doc.text(`Generated: ${new Date().toLocaleString()}`, 8, pageHeight - 7, { align: 'left' });
            this.doc.text("Powered by: Softedumam, Developed by: Asian Coder", pageWidth - 8, pageHeight - 7, { align: 'right' });
        }
    }
    
    addWatermark() {
        const pageWidth = this.doc.internal.pageSize.width;
        const pageHeight = this.doc.internal.pageSize.height;
        const pageCount = this.doc.internal.getNumberOfPages();
        
        for (let i = 1; i <= pageCount; i++) {
            this.doc.setPage(i);
            
            this.doc.setFontSize(60);
            this.doc.setTextColor(240, 240, 240);
            this.doc.setFont('helvetica', 'bold');
            this.doc.text("CONFIDENTIAL", pageWidth / 2, pageHeight / 2, 
                         { align: 'center', angle: 45 });
        }
    }
}