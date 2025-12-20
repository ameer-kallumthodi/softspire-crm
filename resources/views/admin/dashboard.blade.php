@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        border-radius: 10px;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }
    .stat-card-primary .stat-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .stat-card-success .stat-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .stat-card-info .stat-icon {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .stat-card-warning .stat-icon {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    .stat-card-danger .stat-icon {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    .stat-card-secondary .stat-icon {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    }
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }
    .stat-label {
        color: #718096;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .chart-container {
        position: relative;
        height: 350px;
        margin-top: 20px;
    }
    .chart-card {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
    }
    .chart-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    .chart-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-bottom: none;
    }
    .chart-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.25rem;
    }
    .chart-body {
        padding: 25px;
        background: #fff;
    }
    .recent-leads-table {
        border-radius: 0;
        overflow: hidden;
    }
    .recent-leads-table table {
        margin-bottom: 0;
    }
    .recent-leads-table thead {
        background: #f8f9fa;
    }
    .recent-leads-table thead th {
        border-bottom: 2px solid #e9ecef;
        font-weight: 600;
        color: #495057;
        padding: 15px;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .recent-leads-table tbody td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
    }
    .recent-leads-table tbody tr {
        transition: all 0.2s ease;
    }
    .recent-leads-table tbody tr:hover {
        background: #f8f9fa;
        transform: scale(1.01);
    }
    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($totalLeads) }}</div>
                        <div class="stat-label">Total Leads</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($newLeads) }}</div>
                        <div class="stat-label">New Leads (30 Days)</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="user-plus"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($activeLeads) }}</div>
                        <div class="stat-label">Active Leads</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="activity"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($convertedLeads) }}</div>
                        <div class="stat-label">Converted Leads</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Statistics -->
<div class="row mb-4">
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-secondary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($todayLeads) }}</div>
                        <div class="stat-label">Today's Leads</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="calendar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($thisWeekLeads) }}</div>
                        <div class="stat-label">This Week</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="trending-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($thisMonthLeads) }}</div>
                        <div class="stat-label">This Month</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="bar-chart-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($totalUsers) }}</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="user"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mb-4">
    <!-- Chart 1: Leads Over Time (Line Chart) -->
    <div class="col-lg-8 mb-4">
        <div class="card chart-card">
            <div class="chart-header">
                <h4>
                    <i data-feather="trending-up" class="me-2"></i> Leads Over Time (Last 30 Days)
                </h4>
            </div>
            <div class="chart-body">
                <div class="chart-container">
                    <canvas id="leadsOverTimeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart 2: Leads by Source (Doughnut Chart) -->
    <div class="col-lg-4 mb-4">
        <div class="card chart-card">
            <div class="chart-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h4>
                    <i data-feather="pie-chart" class="me-2"></i> Leads by Source
                </h4>
            </div>
            <div class="chart-body">
                <div class="chart-container">
                    <canvas id="leadsBySourceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Leads & Status Distribution -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card chart-card">
            <div class="card-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none; padding: 20px;">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-white">
                        <i data-feather="clock" class="me-2"></i> Recent Leads
                    </h4>
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-light">View All</a>
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive recent-leads-table">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Country</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLeads as $lead)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <span class="text-primary fw-bold">{{ substr($lead->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <strong>{{ $lead->name }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $lead->country_code }} {{ $lead->phone }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $lead->email ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="status-badge bg-primary text-white">
                                        {{ $lead->leadStatus->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $lead->country->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $lead->created_at->format('M d, Y') }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-sm btn-outline-info">
                                        <i data-feather="eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i data-feather="inbox" class="mb-2" style="width: 48px; height: 48px; opacity: 0.3;"></i>
                                    <p class="text-muted mb-0">No leads found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card chart-card">
            <div class="card-header" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border: none; padding: 20px;">
                <h4 class="mb-0 text-white">
                    <i data-feather="pie-chart" class="me-2"></i> Leads by Status
                </h4>
            </div>
            <div class="card-body" style="padding: 25px;">
                @if($leadsByStatus->count() > 0)
                <div class="mb-3">
                    @php
                        $statusColors = [
                            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                            'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                            'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                            'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                            'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                            'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
                        ];
                        $colorIndex = 0;
                    @endphp
                    @foreach($leadsByStatus as $status)
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded" style="background: #f8f9fa; transition: all 0.3s ease;">
                        <div class="d-flex align-items-center">
                            <div class="me-3" style="width: 16px; height: 16px; background: {{ $statusColors[$colorIndex % count($statusColors)] }}; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                            <span class="fw-medium" style="color: #2d3748;">{{ $status['name'] }}</span>
                        </div>
                        <span class="badge" style="background: {{ $statusColors[$colorIndex % count($statusColors)] }}; color: white; padding: 6px 12px; font-size: 0.875rem; font-weight: 600;">{{ $status['count'] }}</span>
                    </div>
                    @php $colorIndex++; @endphp
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i data-feather="bar-chart" class="mb-2" style="width: 48px; height: 48px; opacity: 0.3;"></i>
                    <p class="text-muted mb-0">No status data available</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="{{ asset('assets/libs/chart.js/dist/Chart.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize feather icons with error handling
    if (typeof feather !== 'undefined') {
        try {
            feather.replace();
        } catch(e) {
            console.warn('Feather icons error:', e);
        }
    }

    // Chart 1: Leads Over Time (Line Chart)
    const leadsOverTimeCtx = document.getElementById('leadsOverTimeChart');
    if (leadsOverTimeCtx) {
        new Chart(leadsOverTimeCtx, {
            type: 'line',
            data: {
                labels: @json($leadsOverTimeLabels),
                datasets: [{
                    label: 'Leads',
                    data: @json($leadsOverTime),
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgb(102, 126, 234)',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 13,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return 'Leads: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    }

    // Chart 2: Leads by Source (Doughnut Chart)
    const leadsBySourceCtx = document.getElementById('leadsBySourceChart');
    if (leadsBySourceCtx) {
        const sourceData = @json($leadsBySource);
        const sourceLabels = sourceData.map(item => item.name);
        const sourceCounts = sourceData.map(item => item.count);
        
        // Generate colors dynamically
        const colors = [
            'rgba(102, 126, 234, 0.8)',
            'rgba(245, 87, 108, 0.8)',
            'rgba(79, 172, 254, 0.8)',
            'rgba(67, 233, 123, 0.8)',
            'rgba(250, 112, 154, 0.8)',
            'rgba(48, 207, 208, 0.8)',
            'rgba(118, 75, 162, 0.8)',
            'rgba(240, 147, 251, 0.8)',
        ];
        
        const borderColors = [
            'rgb(102, 126, 234)',
            'rgb(245, 87, 108)',
            'rgb(79, 172, 254)',
            'rgb(67, 233, 123)',
            'rgb(250, 112, 154)',
            'rgb(48, 207, 208)',
            'rgb(118, 75, 162)',
            'rgb(240, 147, 251)',
        ];

        new Chart(leadsBySourceCtx, {
            type: 'doughnut',
            data: {
                labels: sourceLabels,
                datasets: [{
                    data: sourceCounts,
                    backgroundColor: colors.slice(0, sourceLabels.length),
                    borderColor: borderColors.slice(0, sourceLabels.length),
                    borderWidth: 2,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 12,
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: label + ': ' + value + ' (' + percentage + '%)',
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            strokeStyle: data.datasets[0].borderColor[i],
                                            lineWidth: 2,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }
});
</script>
@endpush
