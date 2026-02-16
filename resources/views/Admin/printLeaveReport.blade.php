<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Requests Report - {{ $yearFilter }}</title>
    <style>
        /* A4 Page Setup */
        @page {
            size: A4;
            margin: 2cm 1.5cm;
            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        /* Page Container */
        .page-container {
            width: 100%;
            max-width: 21cm;
            margin: 0 auto;
        }

        /* Header */
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2563eb;
        }

        .report-header h1 {
            font-size: 24pt;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .report-header h2 {
            font-size: 14pt;
            color: #64748b;
            font-weight: normal;
        }

        .report-header .meta {
            margin-top: 10px;
            font-size: 9pt;
            color: #64748b;
        }

        /* Statistics Summary */
        .statistics {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            padding: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            page-break-inside: avoid;
        }

        .stat-box {
            text-align: center;
            padding: 10px;
        }

        .stat-box .number {
            font-size: 28pt;
            font-weight: bold;
            display: block;
        }

        .stat-box.pending .number { color: #eab308; }
        .stat-box.approved .number { color: #16a34a; }
        .stat-box.rejected .number { color: #dc2626; }
        .stat-box.total .number { color: #2563eb; }

        .stat-box .label {
            font-size: 9pt;
            color: #64748b;
            margin-top: 5px;
            display: block;
        }

        /* Section Headers */
        .section-header {
            margin-top: 25px;
            margin-bottom: 15px;
            padding: 12px 15px;
            border-radius: 6px;
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        .section-header.pending {
            background: #fef3c7;
            border-left: 4px solid #eab308;
        }

        .section-header.approved {
            background: #dcfce7;
            border-left: 4px solid #16a34a;
        }

        .section-header.rejected {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
        }

        .section-header h3 {
            font-size: 14pt;
            margin: 0;
        }

        .section-header .count {
            font-weight: normal;
            color: #64748b;
            font-size: 11pt;
        }

        /* Records Grid - 2 columns */
        .records-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        /* Record Card */
        .record-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            background: #ffffff;
            page-break-inside: avoid;
            break-inside: avoid;
            position: relative;
            min-height: 140px;
        }

        .record-card .record-number {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #e2e8f0;
            color: #475569;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: bold;
        }

        .record-card .employee-name {
            font-weight: bold;
            font-size: 11pt;
            color: #1e293b;
            margin-bottom: 8px;
            padding-right: 40px;
        }

        .record-card .detail-row {
            display: flex;
            margin-bottom: 4px;
            font-size: 9pt;
        }

        .record-card .detail-label {
            font-weight: 600;
            color: #64748b;
            width: 70px;
            flex-shrink: 0;
        }

        .record-card .detail-value {
            color: #334155;
            flex: 1;
        }

        .record-card .duration {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            margin-top: 4px;
        }

        .record-card .reason {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #f1f5f9;
            font-size: 8.5pt;
            color: #475569;
            font-style: italic;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: bold;
        }

        .status-badge.pending {
            background: #fef3c7;
            color: #854d0e;
        }

        .status-badge.approved {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge.rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            font-style: italic;
            background: #f8fafc;
            border-radius: 6px;
            page-break-inside: avoid;
        }

        /* Page Footer */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
            padding: 10px 0;
            border-top: 1px solid #e2e8f0;
        }

        /* Page Numbers */
        .page-number {
            position: fixed;
            bottom: 1cm;
            right: 1.5cm;
            font-size: 9pt;
            color: #64748b;
        }

        /* Print Optimizations */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .page-break {
                page-break-before: always;
            }

            .avoid-break {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            /* Ensure 2-column rows stay together */
            .record-row {
                page-break-inside: avoid;
                break-inside: avoid;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                margin-bottom: 12px;
            }
        }

        /* Filters Info */
        .filters-info {
            background: #f1f5f9;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 9pt;
            page-break-inside: avoid;
        }

        .filters-info strong {
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Report Header -->
        <div class="report-header avoid-break">
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 10px;">
                <!-- B-KELANA Logo -->
                <div style="flex-shrink: 0;">
                    <svg width="156" height="49" viewBox="0 0 208 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M57.2292 47.6846C56.1725 51.7536 53.7026 54.6895 50.0194 56.6556C47.136 58.1936 44.0552 58.9243 40.7866 58.9922C38.1766 59.046 35.6064 58.8326 33.131 57.9875C27.4817 56.0593 23.4903 52.2147 20.7073 47.0663C18.8507 43.6308 17.9316 39.9011 17.4216 36.0442C16.8743 31.9128 16.6541 27.7661 16.5893 23.6071C16.5856 23.3753 16.555 23.1448 16.5251 22.7901H7.16328C7.10947 22.6935 7.05443 22.5963 7 22.4984C12.2041 17.3219 17.4069 12.1447 22.5791 7C27.8297 12.2554 33.0337 17.4638 38.2818 22.7155H28.8925C28.5452 23.061 28.4987 23.3399 28.4767 23.6303C28.1715 27.7856 27.933 31.9519 28.1935 36.1097C28.432 39.9127 29.3939 43.5598 31.6492 46.7239C34.3087 50.4548 37.943 52.6024 42.5233 53.2439C46.242 53.7649 49.6494 52.9895 52.8868 51.2106C54.5507 50.2951 55.8074 48.9058 57.2292 47.6833V47.6846Z" fill="#3773B8"/>
                        <path d="M44.3875 51.2217C42.3438 51.0988 40.563 50.6964 38.8984 49.8011V29.0418C40.1086 29.0418 41.2516 28.9947 42.3908 29.0552C43.4274 29.1103 44.2425 29.9285 44.3728 30.9803C44.4602 31.6903 44.4908 32.4106 44.4933 33.1267C44.5043 38.3418 44.5043 43.5562 44.4969 48.7713C44.4969 49.5339 44.4315 50.2971 44.3887 51.2217H44.3875Z" fill="#1C9D67"/>
                        <path d="M36.3624 48.4434C34.1365 46.6357 32.3777 44.6574 31.1645 42.2217C31.0617 42.0181 31.0324 41.7607 31.0312 41.5289C31.0238 36.2135 31.0263 30.8994 31.0263 25.4085C32.2524 25.3914 33.4277 25.222 34.5988 25.5143C35.7876 25.8109 36.5453 26.6768 36.5465 27.8577C36.5538 34.6095 36.5294 41.3607 36.5122 48.1125C36.5122 48.1541 36.4762 48.1957 36.3624 48.4421V48.4434Z" fill="#1C9C67"/>
                        <path d="M52.3867 49.3062C50.6861 50.3561 48.9041 50.909 46.9111 51.0918V33.8456C47.9905 33.6224 49.0484 33.6726 50.0813 33.7772C51.5024 33.9203 52.3549 34.8161 52.3708 36.2654C52.4173 40.6121 52.3879 44.9582 52.3879 49.3074L52.3867 49.3062Z" fill="#1C9C67"/>
                        <path d="M76.7487 28.0953C76.7487 28.9555 76.5734 29.7242 76.223 30.4012C75.8804 31.0783 75.4065 31.6399 74.8011 32.0859C74.1002 32.6116 73.3275 32.986 72.4832 33.209C71.6469 33.4321 70.5835 33.5436 69.2931 33.5436H61.5269V15.7529H68.4328C69.8666 15.7529 70.914 15.8007 71.5752 15.8963C72.2443 15.9919 72.9054 16.203 73.5585 16.5296C74.2356 16.8721 74.7374 17.3341 75.064 17.9155C75.3985 18.489 75.5658 19.1462 75.5658 19.887C75.5658 20.7472 75.3388 21.5079 74.8848 22.169C74.4308 22.8222 73.7895 23.332 72.9611 23.6984V23.794C74.1241 24.025 75.0441 24.5029 75.7211 25.2277C76.4062 25.9526 76.7487 26.9084 76.7487 28.0953ZM70.8702 20.7831C70.8702 20.4883 70.7946 20.1936 70.6432 19.8989C70.4998 19.6042 70.241 19.3851 69.8666 19.2418C69.5321 19.1143 69.1139 19.0466 68.6121 19.0386C68.1182 19.0227 67.4212 19.0147 66.5211 19.0147H66.091V22.7784H66.8079C67.5327 22.7784 68.1501 22.7664 68.6598 22.7425C69.1696 22.7186 69.5719 22.639 69.8666 22.5036C70.2808 22.3204 70.5516 22.0854 70.6791 21.7986C70.8065 21.5039 70.8702 21.1654 70.8702 20.7831ZM71.9933 28.0236C71.9933 27.458 71.8818 27.0239 71.6588 26.7212C71.4437 26.4106 71.0733 26.1796 70.5476 26.0283C70.1892 25.9247 69.6953 25.8689 69.0661 25.861C68.4368 25.853 67.7797 25.849 67.0946 25.849H66.091V30.2818H66.4256C67.7159 30.2818 68.6399 30.2778 69.1975 30.2698C69.7551 30.2618 70.2688 30.1583 70.7388 29.9592C71.2167 29.76 71.5433 29.4972 71.7185 29.1706C71.9017 28.836 71.9933 28.4537 71.9933 28.0236Z" fill="#3773B8"/>
                        <path d="M88.3263 27.3545H79.21V23.9134H88.3263V27.3545Z" fill="#3773B8"/>
                        <path d="M108.53 33.5436H102.903L97.5861 26.4225L96.5108 27.7249V33.5436H91.9227V15.7529H96.5108V23.8059L102.867 15.7529H108.184L101.23 23.9851L108.53 33.5436Z" fill="#3773B8"/>
                        <path d="M123.669 33.5436H110.801V15.7529H123.669V19.194H115.365V22.2646H123.071V25.7057H115.365V30.1025H123.669V33.5436Z" fill="#3773B8"/>
                        <path d="M140.36 33.5436H127.528V15.7529H132.116V30.1025H140.36V33.5436Z" fill="#3773B8"/>
                        <path d="M162.124 33.5436H157.381L156.15 29.9472H149.555L148.324 33.5436H143.7L150.272 15.7529H155.553L162.124 33.5436ZM155.039 26.6854L152.853 20.3051L150.666 26.6854H155.039Z" fill="#3773B8"/>
                        <path d="M180.859 33.5436H176.438L168.887 21.3327V33.5436H164.681V15.7529H170.165L176.653 25.9446V15.7529H180.859V33.5436Z" fill="#3773B8"/>
                        <path d="M201.84 33.5436H197.096L195.865 29.9472H189.27L188.04 33.5436H183.416L189.987 15.7529H195.268L201.84 33.5436ZM194.754 26.6854L192.568 20.3051L190.381 26.6854H194.754Z" fill="#3773B8"/>
                        <path d="M67.707 54.7934H61.3301V53.2442H63.4449V41.1666H61.3301V39.6174H67.707V41.1666H65.5922V53.2442H67.707V54.7934Z" fill="#3773B8"/>
                        <path d="M81.3285 54.7934H79.2897V48.3113C79.2897 47.7881 79.2571 47.2989 79.192 46.8436C79.127 46.3816 79.0077 46.0215 78.8342 45.7633C78.6534 45.4779 78.3931 45.2672 78.0533 45.1314C77.7135 44.9887 77.2725 44.9173 76.7302 44.9173C76.1735 44.9173 75.5915 45.0464 74.9841 45.3046C74.3768 45.5628 73.7948 45.8924 73.2381 46.2933V54.7934H71.1992V43.4089H73.2381V44.6727C73.8743 44.1767 74.5322 43.7894 75.2119 43.5108C75.8915 43.2322 76.5892 43.0929 77.305 43.0929C78.6136 43.0929 79.6114 43.4633 80.2983 44.2039C80.9851 44.9445 81.3285 46.0113 81.3285 47.4042V54.7934Z" fill="#3773B8"/>
                        <path d="M91.5447 54.6915C91.1615 54.7867 90.7421 54.8648 90.2866 54.9259C89.8384 54.9871 89.4371 55.0177 89.0828 55.0177C87.8465 55.0177 86.9066 54.7051 86.2631 54.08C85.6196 53.4549 85.2979 52.4527 85.2979 51.0733V45.0192H83.9205V43.4089H85.2979V40.1372H87.3368V43.4089H91.5447V45.0192H87.3368V50.207C87.3368 50.8049 87.3512 51.2738 87.3801 51.6135C87.4091 51.9465 87.5103 52.259 87.6838 52.5512C87.8429 52.823 88.0598 53.0234 88.3345 53.1525C88.6165 53.2748 89.0431 53.336 89.6142 53.336C89.9468 53.336 90.2939 53.2918 90.6554 53.2035C91.0169 53.1084 91.2772 53.0302 91.4362 52.9691H91.5447V54.6915Z" fill="#3773B8"/>
                        <path d="M104.158 49.2999H95.232C95.232 49.9998 95.3441 50.6113 95.5682 51.1345C95.7924 51.6509 96.0996 52.0756 96.4901 52.4085C96.866 52.7346 97.3107 52.9793 97.824 53.1423C98.3446 53.3054 98.9158 53.3869 99.5376 53.3869C100.362 53.3869 101.19 53.2341 102.021 52.9283C102.86 52.6157 103.456 52.31 103.811 52.011H103.919V54.1004C103.232 54.3722 102.531 54.5998 101.815 54.7833C101.099 54.9667 100.347 55.0584 99.5592 55.0584C97.5493 55.0584 95.9803 54.5488 94.8525 53.5296C93.7246 52.5036 93.1606 51.0496 93.1606 49.1674C93.1606 47.3057 93.6993 45.8278 94.7765 44.7339C95.8611 43.6399 97.2854 43.0929 99.0495 43.0929C100.684 43.0929 101.942 43.5414 102.824 44.4383C103.713 45.3352 104.158 46.6092 104.158 48.2603V49.2999ZM102.173 47.8323C102.166 46.8266 101.895 46.0486 101.36 45.4983C100.832 44.9479 100.026 44.6727 98.9411 44.6727C97.8493 44.6727 96.9781 44.9751 96.3274 45.5798C95.6839 46.1845 95.3188 46.9354 95.232 47.8323H102.173Z" fill="#3773B8"/>
                        <path d="M114.808 45.4983H114.699C114.395 45.4303 114.099 45.3828 113.81 45.3556C113.528 45.3216 113.192 45.3046 112.801 45.3046C112.172 45.3046 111.565 45.4371 110.979 45.7021C110.394 45.9603 109.83 46.2966 109.287 46.7111V54.7934H107.248V43.4089H109.287V45.0906C110.097 44.4791 110.809 44.0476 111.424 43.7962C112.046 43.538 112.678 43.4089 113.322 43.4089C113.676 43.4089 113.933 43.4191 114.092 43.4395C114.251 43.4531 114.489 43.4836 114.808 43.5312V45.4983Z" fill="#3773B8"/>
                        <path d="M126.857 54.7934H124.818V48.3113C124.818 47.7881 124.785 47.2989 124.72 46.8436C124.655 46.3816 124.536 46.0215 124.362 45.7633C124.181 45.4779 123.921 45.2672 123.581 45.1314C123.241 44.9887 122.8 44.9173 122.258 44.9173C121.701 44.9173 121.119 45.0464 120.512 45.3046C119.905 45.5628 119.323 45.8924 118.766 46.2933V54.7934H116.727V43.4089H118.766V44.6727C119.402 44.1767 120.06 43.7894 120.74 43.5108C121.419 43.2322 122.117 43.0929 122.833 43.0929C124.142 43.0929 125.139 43.4633 125.826 44.2039C126.513 44.9445 126.857 46.0113 126.857 47.4042V54.7934Z" fill="#3773B8"/>
                        <path d="M140.196 54.7934H138.168V53.5806C137.987 53.6961 137.741 53.8592 137.431 54.0698C137.127 54.2736 136.83 54.4367 136.541 54.559C136.201 54.7153 135.811 54.8444 135.37 54.9463C134.929 55.055 134.412 55.1094 133.819 55.1094C132.727 55.1094 131.802 54.7697 131.043 54.0902C130.284 53.4107 129.904 52.5444 129.904 51.4912C129.904 50.6283 130.099 49.9318 130.49 49.4018C130.887 48.8651 131.451 48.4438 132.181 48.138C132.919 47.8323 133.805 47.625 134.839 47.5163C135.872 47.4076 136.982 47.326 138.168 47.2717V46.9761C138.168 46.5413 138.085 46.1811 137.919 45.8958C137.76 45.6104 137.528 45.3862 137.224 45.2231C136.935 45.0668 136.588 44.9615 136.183 44.9071C135.778 44.8528 135.355 44.8256 134.914 44.8256C134.379 44.8256 133.783 44.8935 133.125 45.0294C132.467 45.1585 131.787 45.3488 131.086 45.6002H130.978V43.6535C131.375 43.5516 131.95 43.4395 132.702 43.3172C133.454 43.1949 134.195 43.1337 134.925 43.1337C135.778 43.1337 136.52 43.2017 137.149 43.3375C137.785 43.4666 138.334 43.6909 138.797 44.0102C139.253 44.3228 139.6 44.7271 139.838 45.2231C140.077 45.7191 140.196 46.334 140.196 47.0678V54.7934ZM138.168 51.9906V48.8209C137.546 48.8549 136.812 48.9058 135.966 48.9738C135.128 49.0417 134.463 49.1402 133.971 49.2693C133.385 49.4256 132.912 49.6702 132.55 50.0032C132.189 50.3293 132.008 50.7812 132.008 51.3587C132.008 52.011 132.218 52.5036 132.637 52.8366C133.056 53.1627 133.696 53.3258 134.557 53.3258C135.272 53.3258 135.927 53.1967 136.52 52.9385C137.112 52.6735 137.662 52.3575 138.168 51.9906Z" fill="#3773B8"/>
                        <path d="M150.412 54.6915C150.029 54.7867 149.61 54.8648 149.154 54.9259C148.706 54.9871 148.305 55.0177 147.95 55.0177C146.714 55.0177 145.774 54.7051 145.131 54.08C144.487 53.4549 144.165 52.4527 144.165 51.0733V45.0192H142.788V43.4089H144.165V40.1372H146.204V43.4089H150.412V45.0192H146.204V50.207C146.204 50.8049 146.219 51.2738 146.248 51.6135C146.277 51.9465 146.378 52.259 146.551 52.5512C146.71 52.823 146.927 53.0234 147.202 53.1525C147.484 53.2748 147.911 53.336 148.482 53.336C148.814 53.336 149.161 53.2918 149.523 53.2035C149.884 53.1084 150.145 53.0302 150.304 52.9691H150.412V54.6915Z" fill="#3773B8"/>
                        <path d="M155.076 41.503H152.776V39.5155H155.076V41.503ZM154.945 54.7934H152.907V43.4089H154.945V54.7934Z" fill="#3773B8"/>
                        <path d="M169.304 49.1063C169.304 50.9612 168.798 52.4255 167.786 53.4991C166.774 54.5726 165.418 55.1094 163.719 55.1094C162.006 55.1094 160.643 54.5726 159.631 53.4991C158.626 52.4255 158.123 50.9612 158.123 49.1063C158.123 47.2513 158.626 45.787 159.631 44.7135C160.643 43.6331 162.006 43.0929 163.719 43.0929C165.418 43.0929 166.774 43.6331 167.786 44.7135C168.798 45.787 169.304 47.2513 169.304 49.1063ZM167.2 49.1063C167.2 47.6318 166.893 46.5379 166.279 45.8244C165.664 45.1042 164.811 44.7441 163.719 44.7441C162.613 44.7441 161.753 45.1042 161.138 45.8244C160.531 46.5379 160.227 47.6318 160.227 49.1063C160.227 50.5332 160.534 51.6169 161.149 52.3575C161.763 53.0914 162.62 53.4583 163.719 53.4583C164.804 53.4583 165.653 53.0948 166.268 52.3677C166.89 51.6339 167.2 50.5467 167.2 49.1063Z" fill="#3773B8"/>
                        <path d="M182.601 54.7934H180.562V48.3113C180.562 47.7881 180.529 47.2989 180.464 46.8436C180.399 46.3816 180.28 46.0215 180.106 45.7633C179.925 45.4779 179.665 45.2672 179.325 45.1314C178.986 44.9887 178.545 44.9173 178.002 44.9173C177.446 44.9173 176.864 45.0464 176.256 45.3046C175.649 45.5628 175.067 45.8924 174.51 46.2933V54.7934H172.471V43.4089H174.51V44.6727C175.146 44.1767 175.804 43.7894 176.484 43.5108C177.164 43.2322 177.861 43.0929 178.577 43.0929C179.886 43.0929 180.883 43.4633 181.57 44.2039C182.257 44.9445 182.601 46.0113 182.601 47.4042V54.7934Z" fill="#3773B8"/>
                        <path d="M195.94 54.7934H193.912V53.5806C193.731 53.6961 193.486 53.8592 193.175 54.0698C192.871 54.2736 192.575 54.4367 192.285 54.559C191.946 54.7153 191.555 54.8444 191.114 54.9463C190.673 55.055 190.156 55.1094 189.563 55.1094C188.471 55.1094 187.546 54.7697 186.787 54.0902C186.028 53.4107 185.648 52.5444 185.648 51.4912C185.648 50.6283 185.843 49.9318 186.234 49.4018C186.631 48.8651 187.195 48.4438 187.926 48.138C188.663 47.8323 189.549 47.625 190.583 47.5163C191.617 47.4076 192.726 47.326 193.912 47.2717V46.9761C193.912 46.5413 193.829 46.1811 193.663 45.8958C193.504 45.6104 193.272 45.3862 192.969 45.2231C192.679 45.0668 192.332 44.9615 191.927 44.9071C191.523 44.8528 191.1 44.8256 190.659 44.8256C190.124 44.8256 189.527 44.8935 188.869 45.0294C188.211 45.1585 187.532 45.3488 186.83 45.6002H186.722V43.6535C187.119 43.5516 187.694 43.4395 188.446 43.3172C189.198 43.1949 189.939 43.1337 190.669 43.1337C191.523 43.1337 192.264 43.2017 192.893 43.3375C193.529 43.4666 194.078 43.6909 194.541 44.0102C194.997 44.3228 195.344 44.7271 195.582 45.2231C195.821 45.7191 195.94 46.334 195.94 47.0678V54.7934ZM193.912 51.9906V48.8209C193.29 48.8549 192.556 48.9058 191.711 48.9738C190.872 49.0417 190.207 49.1402 189.715 49.2693C189.129 49.4256 188.656 49.6702 188.294 50.0032C187.933 50.3293 187.752 50.7812 187.752 51.3587C187.752 52.011 187.962 52.5036 188.381 52.8366C188.8 53.1627 189.44 53.3258 190.301 53.3258C191.016 53.3258 191.671 53.1967 192.264 52.9385C192.857 52.6735 193.406 52.3575 193.912 51.9906Z" fill="#3773B8"/>
                        <path d="M201.927 54.7934H199.888V38.9346H201.927V54.7934Z" fill="#3773B8"/>
                    </svg>
                </div>
                <!-- Title Section -->
                <div style="flex-grow: 1;">
                    <h1 style="margin: 0;">Leave Requests Report</h1>
                    <h2 style="margin: 5px 0 0 0;">{{ $departmentFilter ?: 'All Departments' }}</h2>
                </div>
            </div>
            <div class="meta">
                Generated on {{ now()->format('F d, Y \a\t h:i A') }}
                @if($departmentFilter || $yearFilter)
                    <div class="filters-info" style="margin-top: 10px;">
                        @if($departmentFilter)
                            <strong>Department:</strong> {{ $departmentFilter }}
                        @endif
                        @if($yearFilter)
                            <strong>Year:</strong> {{ $yearFilter }}
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics Summary -->
        <div class="statistics avoid-break">
            <div class="stat-box pending">
                <span class="number">{{ $stats['pending'] }}</span>
                <span class="label">Pending</span>
            </div>
            <div class="stat-box approved">
                <span class="number">{{ $stats['approved'] }}</span>
                <span class="label">Approved</span>
            </div>
            <div class="stat-box rejected">
                <span class="number">{{ $stats['rejected'] }}</span>
                <span class="label">Rejected</span>
            </div>
            <div class="stat-box total">
                <span class="number">{{ $stats['total'] }}</span>
                <span class="label">Total Requests</span>
            </div>
        </div>

        @php
            $pendingRequests = $leaveRequests->filter(fn($r) => strtolower($r->status) === 'pending');
            $approvedRequests = $leaveRequests->filter(fn($r) => strtolower($r->status) === 'approved');
            $rejectedRequests = $leaveRequests->filter(fn($r) => strtolower($r->status) === 'rejected');
        @endphp

        <!-- Pending Requests Section -->
        @if($pendingRequests->count() > 0)
            <div class="section-header pending avoid-break">
                <h3>Pending Requests <span class="count">({{ $pendingRequests->count() }})</span></h3>
            </div>

            @foreach($pendingRequests->chunk(2) as $chunk)
                <div class="record-row">
                    @foreach($chunk as $index => $request)
                        <div class="record-card avoid-break">
                            <div class="record-number">#{{ $loop->parent->index * 2 + $loop->index + 1 }}</div>
                            <div class="employee-name">{{ $request->employee->user->name }}</div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Leave Type:</span>
                                <span class="detail-value">{{ $request->leave_type }}</span>
                            </div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Period:</span>
                                <span class="detail-value">{{ \Carbon\Carbon::parse($request->leave_from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($request->leave_to)->format('M d, Y') }}</span>
                            </div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Submitted:</span>
                                <span class="detail-value">{{ $request->created_at->format('M d, Y') }}</span>
                            </div>

                            <div class="duration">
                                {{ \Carbon\Carbon::parse($request->leave_from)->diffInDays(\Carbon\Carbon::parse($request->leave_to)) + 1 }} 
                                {{ \Carbon\Carbon::parse($request->leave_from)->diffInDays(\Carbon\Carbon::parse($request->leave_to)) + 1 === 1 ? 'day' : 'days' }}
                            </div>

                            @if($request->reason)
                                <div class="reason">{{ Str::limit($request->reason, 80) }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif

        <!-- Approved Requests Section -->
        @if($approvedRequests->count() > 0)
            <div class="section-header approved avoid-break" style="margin-top: 30px;">
                <h3>Approved Requests <span class="count">({{ $approvedRequests->count() }})</span></h3>
            </div>

            @foreach($approvedRequests->chunk(2) as $chunk)
                <div class="record-row">
                    @foreach($chunk as $index => $request)
                        <div class="record-card avoid-break">
                            <div class="record-number">#{{ $loop->parent->index * 2 + $loop->index + 1 }}</div>
                            <div class="employee-name">{{ $request->employee->user->name }}</div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Leave Type:</span>
                                <span class="detail-value">{{ $request->leave_type }}</span>
                            </div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Period:</span>
                                <span class="detail-value">{{ \Carbon\Carbon::parse($request->leave_from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($request->leave_to)->format('M d, Y') }}</span>
                            </div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Approved:</span>
                                <span class="detail-value">{{ $request->updated_at->format('M d, Y') }}</span>
                            </div>

                            <div class="duration">
                                {{ \Carbon\Carbon::parse($request->leave_from)->diffInDays(\Carbon\Carbon::parse($request->leave_to)) + 1 }} 
                                {{ \Carbon\Carbon::parse($request->leave_from)->diffInDays(\Carbon\Carbon::parse($request->leave_to)) + 1 === 1 ? 'day' : 'days' }}
                            </div>

                            @if($request->reason)
                                <div class="reason">{{ Str::limit($request->reason, 80) }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif

        <!-- Rejected Requests Section -->
        @if($rejectedRequests->count() > 0)
            <div class="section-header rejected avoid-break" style="margin-top: 30px;">
                <h3>Rejected Requests <span class="count">({{ $rejectedRequests->count() }})</span></h3>
            </div>

            @foreach($rejectedRequests->chunk(2) as $chunk)
                <div class="record-row">
                    @foreach($chunk as $index => $request)
                        <div class="record-card avoid-break">
                            <div class="record-number">#{{ $loop->parent->index * 2 + $loop->index + 1 }}</div>
                            <div class="employee-name">{{ $request->employee->user->name }}</div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Leave Type:</span>
                                <span class="detail-value">{{ $request->leave_type }}</span>
                            </div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Period:</span>
                                <span class="detail-value">{{ \Carbon\Carbon::parse($request->leave_from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($request->leave_to)->format('M d, Y') }}</span>
                            </div>
                            
                            <div class="detail-row">
                                <span class="detail-label">Rejected:</span>
                                <span class="detail-value">{{ $request->updated_at->format('M d, Y') }}</span>
                            </div>

                            <div class="duration">
                                {{ \Carbon\Carbon::parse($request->leave_from)->diffInDays(\Carbon\Carbon::parse($request->leave_to)) + 1 }} 
                                {{ \Carbon\Carbon::parse($request->leave_from)->diffInDays(\Carbon\Carbon::parse($request->leave_to)) + 1 === 1 ? 'day' : 'days' }}
                            </div>

                            @if($request->reason)
                                <div class="reason">{{ Str::limit($request->reason, 80) }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif

        <!-- Empty State -->
        @if($leaveRequests->count() === 0)
            <div class="empty-state">
                <p style="font-size: 14pt; margin-bottom: 10px;">📋</p>
                <p>No leave requests found for the selected criteria</p>
            </div>
        @endif

        <!-- Report Footer -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #e2e8f0; text-align: center; font-size: 8pt; color: #94a3b8; page-break-inside: avoid;">
            <p>This is a computer-generated report. No signature required.</p>
            <p style="margin-top: 5px;">For inquiries, contact the Human Resources Department</p>
        </div>
    </div>

    <script>
        // Add page numbers after print
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>