import * as React from 'react';
import { useEffect, useState } from 'react';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import { Button, ButtonType } from '../Button';

interface DownloadPdfButtonProps {
    chartRef: React.RefObject<HTMLDivElement>,
    isDisabled: boolean
}

export function DownloadPdfButton({chartRef, isDisabled}: DownloadPdfButtonProps) {
    const [isButtonDisabled, setDisable] = useState(true);

    useEffect(() => {
        setDisable(!chartRef.current);
    }, [chartRef]);

    const downloadPdf = (
        imgWidth: number,
        imgHeight: number,
        imgFormat: string,
        imgPlaceXAxis: number,
        imgPlaceYAxis: number
    ) => {
        html2canvas(chartRef.current).then((canvas) => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF();
            pdf.addImage(imgData, imgFormat, imgPlaceXAxis, imgPlaceYAxis, imgWidth, imgHeight);
            pdf.save('chart.pdf');
        });
    };

    const handleClick = () => {
        downloadPdf(200, 100, 'PNG', 10, 10);
    };

    if (isButtonDisabled) {
        return <></>;
    }

    return <Button
        className={'btn btn-block btn-warning'}
        type={ButtonType.Button}
        text={'Download PDF'}
        onClick={(e) => handleClick()}
        isDisabled={isDisabled}
    />;
}
