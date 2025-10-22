<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARCA.POS.Terminal</title>
    <style>
        .offline-banner {
            background-color: #f9a825;
            color: #fff;
            text-align: center;
            padding: 10px;
            font-weight: bold;
        }

        .sync-banner {
            background-color: #4caf50;
            color: #fff;
            text-align: center;
            padding: 10px;
            font-weight: bold;
        }

        .sync-progress {
            margin-top: 10px;
        }

        .force-sync-btn {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div id="app">
        <div v-if="isOffline" class="offline-banner">
            Offline Mode Active
        </div>

        <div v-if="isSyncing" class="sync-banner">
            Syncing offline sales... please wait.
            <div class="sync-progress">
                <progress :value="syncProgress" max="100"></progress>
                <p>Synced: @{{ syncedCount }} / @{{ totalToSync }} transactions</p>
                <p>Errors: @{{ errorCount }}</p>
            </div>
        </div>

        <!-- POS Terminal Interface -->
        <div>
            <!-- Sale creation form -->
        </div>

        <button @click="forceSync" class="force-sync-btn">Force Sync</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script>
        new Vue({
            el: '#app',
            data: {
                isOffline: false,
                isSyncing: false,
                syncProgress: 0,
                syncedCount: 0,
                totalToSync: 0,
                errorCount: 0,
            },
            methods: {
                checkNetworkStatus() {
                    // Simulate network status check
                    this.isOffline = !navigator.onLine;
                },
                forceSync() {
                    this.isSyncing = true;
                    // Simulate sync process
                    let interval = setInterval(() => {
                        this.syncProgress += 10;
                        this.syncedCount = Math.floor(this.totalToSync * (this.syncProgress / 100));
                        if (this.syncProgress >= 100) {
                            clearInterval(interval);
                            this.isSyncing = false;
                        }
                    }, 500);
                },
            },
            created() {
                this.checkNetworkStatus();
                setInterval(this.checkNetworkStatus, 5000); // Check every 5 seconds
            },
        });
    </script>
</body>
</html>
