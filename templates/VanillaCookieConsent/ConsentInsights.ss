<% with $SiteConfig.CCInsightData %>
    <h2 style="margin-top: 2rem">Cookie Consent Insights</h2>
    <p>
        The displayed data is based on the last <strong>$SavePeriodForInsights days</strong> of cookie consent data.<br>
        Currently we have data for <strong>$Consents.Total</strong> consents.
    </p>

    <div style="display: flex; flex-direction: row; margin-bottom: 4rem">
        <div>
            <h3>Accept / Reject Rate</h3>
            <span>Last $SavePeriodForInsights days</span>
            <div>
                <canvas
                    id="acceptRejectRateChart"
                    data-accepted="$Consents.Accepted"
                    data-partly="$Consents.Partly"
                    data-rejected="$Consents.Rejected"
                    style="width: 100%; height: 400px;"
                ></canvas>
            </div>
        </div>

        <div style="margin-left: 2rem">
            <h3>Accepted vs Rejected Cookies by Category</h3>
            <span>Last $SavePeriodForInsights days</span>
            <div>
                <canvas
                    id="acceptRejectByCategoryChart"
                    data-categories="$Categories"
                    style="width: 100%; height: 400px;"
                ></canvas>
            </div>
            <span style="font-size: small; color:#9c9595">Note: Nessesary cookies can not be rejected.</span>
        </div>
    </div>
<% end_with %>
